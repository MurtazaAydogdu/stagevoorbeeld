#!/usr/bin/env bash

# usage: ./operations/setVersion.sh [version] [hash]
# updates version in projects file
# when version == "base" the BASE_VERSION is also updated.
# when version == "hash" the APP_VERSION is completed with [hash] (used in image creation)

current_dir=$(cd $(dirname $0) && pwd)
base_dir=${current_dir}/..
src_dir=${base_dir}/src

[ -z "${OPS_HOME}" ] && echo "env not set run \`source setenv\`" && exit 1

source ${OPS_HOME}/utils/echo.sh


# Try to figure out the os and arch for binary fetching
uname="$(uname -a)"
os=
arch=x86
case "$uname" in
  Linux\ *) os=linux ;;
  Darwin\ *) os=darwin ;;
  SunOS\ *) os=sunos ;;
esac
case "$uname" in
  *x86_64*) arch=x64 ;;
  *raspberrypi*) arch=arm-pi ;;
esac

if [[ "${os}" == 'linux' ]]; then
   SED='sed -i_orig '
   CURL='wget -O '
elif [[ "${os}" == 'darwin' ]]; then
    SED='sed -i _orig'
    CURL='curl -O '
fi

return_val="0"
if [ -z "${FORCE}" ]; then
  git status | grep "nothing to commit, working tree clean"
  return_val=$?
  if [ "${return_val}" != "0" ]; then
    git status | grep "nothing to commit, working directory clean"
    return_val=$?
  fi
fi

#echo return_val=${return_val}
if [ "${return_val}" != "0" ]; then

  if [ -z "${FORCE}" ]; then
    git status
    read -r -p "QUEST:  Working tree NOT clean; Are you sure? [y/N] " response
    if [[ $response =~ ^([yY][eE][sS]|[yY])$ ]]; then
      echo_warn "continuing with dirty working tree"
    else
      echo_error "---> version update ABORTED!!"
      exit 1
    fi
  fi
fi

param_version=${2}
base=${1}
hash=${1}

app_prefix="APP_VERSION="
base_prefix="BASE_VERSION="
prefix=${app_prefix}
if [ "${base}" != "base" ]; then
  param_version=${base}
fi

version_source=${base_dir}/.version
echo_info "version file found at ${version_source}"
current_version_line=$(cat ${version_source} | grep ${prefix})
current_version=$(echo ${current_version_line} | sed -n -e "s/^.*${prefix}//p")
if [ "${hash}" == "hash" ]; then
  param_version=""
  hashRev=${2}
else
  hashRev=$(git rev-parse HEAD)
fi
echo_info "current version: ${current_version}"
current_version_first=$(echo ${current_version} | cut -d '-' -f 1)
if [ -z "${param_version}" ]; then

  read -r -p "QUEST:   update version:[${current_version_first}] -> " response
  [ -z "${response}" ] && response=${current_version_first}

else
  response=${param_version}
fi
new_version=${response}
new_hash=${hashRev:0:10}
new_hashed_version=${new_version}-${new_hash}
echo_info "new version-hash:${new_hashed_version}"

if [ "${hash}" == "hash" ]; then
  new_version=${new_hashed_version}
fi


if [ -z "${FORCE}" ]; then
read -r -p "QUEST:  Are you sure? [y/N] " response
if [[ $response =~ ^([yY][eE][sS]|[yY])$ ]]; then
  echo_info "updating ${version_source}"
else
  echo_error "---> version update ABORTED!!"
  exit 1
fi
fi

[ "${base}" == "base" ] && ${SED} -e "s/${base_prefix}.*$/${base_prefix}${new_hashed_version}/g" ${version_source}
${SED} -e "s/${app_prefix}.*$/${app_prefix}${new_version}/g" ${version_source}

echo_debug "cleaning up"
rm ${version_source}_orig

if [ "${base}" == "base" ]; then
  echo_info "committing .version"
  git commit ${version_source}  -m"base version ${new_hashed_version}"
  source setenv
  echo_debug "building images"
  dobi -f dobi.build.images.yaml
  echo_debug "pushing images"
  dobi -f dobi.build.images.yaml push
elif [ "${hash}" != "hash" ]; then
  echo_info "committing .version"
  git commit ${version_source} -m"app version ${new_version}"
fi
echo_info "done."
