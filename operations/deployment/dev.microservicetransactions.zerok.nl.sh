#!/usr/bin/env bash
current_dir=$(cd $(dirname $0) && pwd)
cd ${current_dir}

VHOST=dev.microservicetransactions.zerok.nl

ADAPTER_CONFIG=$(cat $PWD/../../etc/config.json | base64) \
./deploy.sh root@192.168.0.127 ${VHOST} 