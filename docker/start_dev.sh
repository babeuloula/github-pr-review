#!/usr/bin/env bash

set -e

readonly DOCKER_PATH=$(dirname $(realpath $0))

. $DOCKER_PATH/functions.sh

checkRequirements

readonly HTTP_HOST="github.gui"
addHost $HTTP_HOST

cd $(dirname $DOCKER_PATH)

for line in $(cat .env.dist)
do
    key=$(echo ${line} | awk -F "=" '{print $1}')
    defaultValue=$(echo ${line} | awk -F "${key} *= *" '{print $2}')
    configureEnv ${key} $(getEnvValue ${key} ${defaultValue})
done

configureEnv 'VERSION' $(getCurrentVersion)

. $DOCKER_PATH/../.env

cd $DOCKER_PATH

for line in $(cat .env.dev.dist)
do
    key=$(echo ${line} | awk -F "=" '{print $1}')
    defaultValue=$(echo ${line} | awk -F "${key} *= *" '{print $2}')
    configureEnv ${key} $(getEnvValue ${key} ${defaultValue})
done

. $DOCKER_PATH/.env

dockerStart 'dev'

installComposer 'dev'

waitMysql

databaseAndMigrations

docker-compose exec php bin/console cache:clear

echo -e "${GREEN}Environment is started, you can go to http://$HTTP_HOST:$HTTP_PORT ${RESET}" > /dev/tty
