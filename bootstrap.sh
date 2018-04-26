#!/usr/bin/env bash

# Exit on any error
set -e

if [ ${OPS_VERBOSE+x} ]; then set -v; fi

current_dir=$(cd $(dirname $0) && pwd)
root_dir=${current_dir}
cd ${root_dir}

[ -z "${OPS_HOME}" ] && echo "env not set run \`source setenv\`" && exit 1

source ${OPS_HOME}/utils/echo.sh
source ${OPS_HOME}/utils/ensure.sh

banner "This script will configure Docker for use."

#if [ ! -f ./setenv.shared ]; then
#  scp shared@opscentre.informaat-cxp.com:setenv.shared ./setenv.shared
#  echo_error "Shared keys have been copied, please rerun command."
#  exit 1
#fi

# Check whether MACHINE_NAME and DOCKER_MACHINE_NAME are set in env. Otherwise it defaults to `microservicetransactions`.
MACHINE_NAME=${MACHINE_NAME:-'microservicetransactions'}
# DOCKER_MACHINE_NAME can be set by previous calls of `eval "$(docker-machine env ${MACHINE_NAME})"`
DOCKER_MACHINE_NAME=${DOCKER_MACHINE_NAME:-'microservicetransactions'}

# Check the docker toolbox is installed:
[ $(ensure docker docker-machine docker-compose) ] \
  && echo_error "Docker Toolbox not found. Download it here: https://github.com/docker/toolbox/releases" \
  && exit 1

# Create the docker image:
# check the machine isn't already there:
if [ "$(docker-machine ls | grep ^${MACHINE_NAME})" != "" ]; then
  echo_skip "docker machine \"${MACHINE_NAME}\" exists already, skipping creation"
else
  echo_info "Creating new docker machine \"${bold}${MACHINE_NAME}${normal}\""
  docker-machine create -d virtualbox --virtualbox-hostonly-cidr=192.168.88.1/24 ${MACHINE_NAME}
fi

# Start the image and make active
if [ "$(docker-machine status ${MACHINE_NAME})" == "Running" ]; then
  echo_skip "${bold}${MACHINE_NAME}${normal} machine is already running, no need to start it again"
else
  echo_info "Starting the ${bold}${MACHINE_NAME}${normal} docker machine ${MACHINE_NAME}"
  docker-machine start ${MACHINE_NAME}
  sleep 3 && echo -n "." && sleep 3 && echo -n "." && sleep 3 && echo ".done"
fi

# Check the docker env and reset when switching machine_name
if [ -z "${DOCKER_HOST}" -o "${MACHINE_NAME}" != "${DOCKER_MACHINE_NAME}" ]; then
  echo
  echo_info "Setting the docker variables in your ENV based on the \"${MACHINE_NAME}\" machine"
  eval "$(docker-machine env ${MACHINE_NAME})"

  echo_info "Add these env vars to your .profile, .bash_rc or equivalent:"
  echo "$(docker-machine env ${MACHINE_NAME})"
  echo
fi

# add ${HOME}/bin to PATH
LOCAL_BIN=${HOME}/bin
case ":$PATH:" in
  *":$LOCAL_BIN:"*) :;; # already there
  *) export PATH="$LOCAL_BIN:$PATH";; # or PATH="$PATH:$new_entry"
esac

if [ ! $(command -v ${LOCAL_BIN}/dobi) ]; then
    mkdir -p ${LOCAL_BIN}
    curl -L -o ${LOCAL_BIN}/dobi "https://github.com/dnephin/dobi/releases/download/v0.8/dobi-$(uname -s)"
    chmod +x ${LOCAL_BIN}/dobi
fi

if [ ! $(command -v ${LOCAL_BIN}/rocker) ]; then
    mkdir -p ${LOCAL_BIN}
    curl -SL "https://github.com/grammarly/rocker/releases/download/1.3.0/rocker_darwin_amd64.tar.gz" | tar -xzC ${LOCAL_BIN}
    chmod +x ${LOCAL_BIN}/rocker
fi

echo
echo_info "Done. Docker is configured for the project in this shell."
