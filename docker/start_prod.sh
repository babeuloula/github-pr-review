#!/usr/bin/env bash

set -e

readonly DOCKER_PATH=$(dirname $(realpath $0))

. $DOCKER_PATH/functions.sh

cd $(dirname $0)

for line in $(cat .env.dist)
do
    key=$(echo ${line} | awk -F "=" '{print $1}')
    defaultValue=$(echo ${line} | awk -F "${key} *= *" '{print $2}')
    configureEnv ${key} $(getEnvValue ${key} ${defaultValue})
done

docker-compose -f docker-compose.yml -f docker-compose.prod.yml build --parallel
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d --remove-orphans

docker-compose exec php bin/console cache:clear
