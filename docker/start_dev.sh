#!/usr/bin/env bash

set -e

readonly DOCKER_PATH=$(dirname $(realpath $0))

. $DOCKER_PATH/functions.sh

readonly HTTP_HOST="github.gui"
addHost $HTTP_HOST

cd $(dirname $0)

for line in $(cat .env.dist)
do
    key=$(echo ${line} | awk -F "=" '{print $1}')
    defaultValue=$(echo ${line} | awk -F "${key} *= *" '{print $2}')
    configureEnv ${key} $(getEnvValue ${key} ${defaultValue})
done

docker-compose -f docker-compose.yml -f docker-compose.dev.yml build --parallel
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d --remove-orphans

. $DOCKER_PATH/.env
block 42 "Environment is started, you can go to http://$HTTP_HOST:$HTTP_PORT"
