#!/usr/bin/env bash

echo "cleanup"
# git tag is run as root from container... correct permissions on workspace
sudo chown -R jenkins:jenkins .git
docker-compose -p microservicetransactions down --volumes --remove-orphans
docker ps -aq |xargs docker rm
docker volume ls -q |xargs docker volume rm
