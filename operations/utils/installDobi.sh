#!/usr/bin/env bash

current_dir=$(cd $(dirname $0) && pwd)
root_dir=${current_dir}/../..
source ${root_dir}/operations/utils/echo.sh
source ./echo.sh

echo_info "Exporting bin to path"

# add ${HOME}/bin to PATH
LOCAL_BIN=${HOME}/bin
case ":$PATH:" in
  *":$LOCAL_BIN:"*) :;; # already there
  *) export PATH="$LOCAL_BIN:$PATH";; # or PATH="$PATH:$new_entry"
esac

if [ ! $(command -v ${LOCAL_BIN}/dobi) ]; then
	echo_info "Dobi not found, curling..."
    mkdir -p ${LOCAL_BIN}
    curl -L -o ${LOCAL_BIN}/dobi "https://github.com/dnephin/dobi/releases/download/v0.8/dobi-$(uname -s)"
    chmod +x ${LOCAL_BIN}/dobi
fi

echo_info "Done."