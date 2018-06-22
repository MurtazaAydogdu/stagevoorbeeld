#!/usr/bin/env bash

# deploy.sh host [baseVhost]
# eg $ ./operations/deployment/deploy.dev.microstore.bjoola.com.sh dev.microstore.bjoola.com dev.microstore.bjoola.com 

host=${1}
[ -z ${host} ] && echo "host missing" && exit 1
vhost=${2}
[ -z ${vhost} ] && echo "vhostDomain missing" && vhost=dev.microservicetransactions.zerok.nl

SSH_ARGS=${SSH_ARGS:-"-q -oPasswordAuthentication=no -o PreferredAuthentications=publickey"}
SSH="ssh ${SSH_ARGS}"

composeFolder=$(echo ${vhost} | sed 's/[^a-zA-Z0-9]//g')

current_dir=$(cd $(dirname $0) && pwd)
root_dir=${current_dir}/../..
cd ${current_dir}

set -e
set -x

${SSH} ${host} -t "docker network create --driver=overlay traefik-net || true"

# deploy
${SSH} ${host} mkdir -p ./branch/${composeFolder}

cat ${root_dir}/docker-compose.release.yml | ${SSH} ${host} "cat > branch/${composeFolder}/docker-compose.yml"

export GIT_COMMIT=$(git rev-parse HEAD)
export VERSION=${VERSION:-${APP_VERSION}-${GIT_COMMIT:0:10}}
echo VERSION=${VERSION} | ${SSH} ${host} "cat > branch/${composeFolder}/.version"
echo VIRTUAL_HOST=${vhost} | ${SSH} ${host} "cat >> branch/${composeFolder}/.version"

if [ -n "${DB_PASSWORD}" ]; then
  echo DB_PASSWORD=${DB_PASSWORD} | ${SSH} ${host} "cat >> branch/${composeFolder}/.version"
fi

if [ -n "${DB_USERNAME}" ]; then
  echo DB_USERNAME=${DB_USERNAME} | ${SSH} ${host} "cat >> branch/${composeFolder}/.version"
fi

${SSH} ${host} -t "export \$(cat branch/${composeFolder}/.version | xargs) && docker stack deploy --with-registry-auth ${composeFolder} -c branch/${composeFolder}/docker-compose.yml --prune"
${SSH} ${host} -t "export \$(cat branch/${composeFolder}/.version | xargs) && docker run --rm -e DB_CONNECTION=mysql -e DB_HOST=192.168.0.227 -e DB_PORT=3306 -e DB_DATABASE=dev_microservicetransactions -e DB_USERNAME=${DB_USERNAME} -e DB_PASSWORD=${DB_PASSWORD} docker-registry.bjoola.nl/microservicetransactions/release:${VERSION} -- php artisan migrate --force"
# ${SSH} ${host} -t "export \$(cat branch/${composeFolder}/.version | xargs) && docker service create --restart-condition=none --name migrate_microservicetransactions -e DB_CONNECTION=mysql -e DB_HOST=192.168.0.227 -e DB_PORT=3306 -e DB_DATABASE=dev_bank_export_converter -e DB_USERNAME=${DB_USERNAME} -e DB_PASSWORD=${DB_PASSWORD} -d docker-registry.bjoola.nl/microservicetransactions/release:${VERSION} migrate"

# see deploy log
#${SSH} ${host} -t "export \$(cat branch/${composeFolder}/.version | xargs) && ./docker-compose -f branch/${composeFolder}/docker-compose.yml logs -f"

# Function that creates payload with deployment info
generate_webhook_data()
{
  cat <<EOF
  {
  	"channel": "#microservices",
  	"username": "Deploy Bot",
  	"icon_emoji": ":robot_face:",
  	"attachments": [{
  		"fallback": "Bank Export Converter deployed successfully: ${VERSION:-${APP_VERSION}-${GIT_COMMIT:0:10}}",
  		"pretext": "Bank Export Converter deployed successfully: ${VERSION:-${APP_VERSION}-${GIT_COMMIT:0:10}}",
  		"color": "#7EF66B",
  		"fields": [{
  			"title": "Version",
  			"value": "${VERSION:-${APP_VERSION}-${GIT_COMMIT:0:10}}",
  			"short": false
  		}, {
  			"title": "Environment",
  			"value": "<https://${vhost}|${vhost}>",
  			"short": false
  		}]
  	}]
  }
EOF
}

# Send notifications to slack channel
if [ -n "${SLACK_URL}" ]; then
	curl -X POST --data-urlencode "payload=$(generate_webhook_data)" -s ${SLACK_URL} -o /dev/null
fi

echo "https://${vhost}"