#!/usr/bin/env bash

current_dir=$(cd $(dirname $0) && pwd)
base_dir=${current_dir}/../..

cd ${base_dir}

EXTRA_ARGS=""
if [ "${1}" == "attach" ]; then
EXTRA_ARGS="--attach"
fi

if [ "${CI}" != "1" ]; then
  source setenv
  # ensure Virtualbox flushes fs caches
  docker-machine ssh ${DOCKER_MACHINE_NAME} "echo 3 | sudo tee /proc/sys/vm/drop_caches"
  export GIT_COMMIT=$(git rev-parse HEAD)
else
  # extra steps on JENKINS
  set -e
  EXTRA_ARGS="--push"
  git config --global user.email "${ghprbActualCommitAuthorEmail:-github@bjoola.nl}"
  git config --global user.name "${ghprbActualCommitAuthor:-GitHub}"
  [ -n "${ghprbActualCommit}" ] && export GIT_COMMIT=${ghprbActualCommit}
fi

CACHE_DATE=$(date +%Y-%m-%d:%H:%M:%S)
export VERSION=${APP_VERSION}-${GIT_COMMIT:0:10}
dobi -f dobi.build.release.yaml
rocker build \
  -f ${current_dir}/Rockerfile \
  -var Hash=${GIT_COMMIT:0:10} \
  -var AppVersion=${VERSION} \
  -var BaseVersion=${BASE_VERSION} \
  -var CACHE_DATE=${CACHE_DATE} \
  -var GITHUB_ACCESS_TOKEN=${GITHUB_ACCESS_TOKEN} \
  ${EXTRA_ARGS} \
  .

if [ "${1}" == "run" ]; then
  docker run --rm -it -p 80:80 docker-registry.bjoola.nl/microservicetransactions/release:${VERSION}
fi

#cleanup on CI
[ "${CI}" == "1" ] && docker rmi docker-registry.bjoola.nl/microservicetransactions/release:${VERSION}