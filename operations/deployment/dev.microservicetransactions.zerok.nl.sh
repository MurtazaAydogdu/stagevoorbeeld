#!/usr/bin/env bash
current_dir=$(cd $(dirname $0) && pwd)
cd ${current_dir}

ADAPTER_CONFIG=$(cat $PWD/../../etc/config.json | base64)
VHOST=dev.microservicetransactions.zerok.nl

./deploy.sh root@192.168.0.127 ${VHOST} 