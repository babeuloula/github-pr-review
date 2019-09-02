#!/usr/bin/env sh

set -e

readonly DOCKER_PATH=$(dirname $(realpath $0))

function addHost() {
    local host=$1
    if [ "$(cat /etc/hosts | grep -c ${host})" -eq 0 ]; then
        sudo /bin/sh -c "echo \"127.0.0.1 ${host}\" >> /etc/hosts"
    fi
}

readonly HTTP_HOST="github.gui"
addHost $HTTP_HOST

cd $(dirname $0)

function configureEnv() {
    local key=$1
    local value=$2

    if [ ! -f .env ] || [ "$(cat .env | grep -Ec "^${key}=(.*)$")" -eq 0 ]; then
        echo "${key}=${value}" >> .env
    else
        sed "s/^${key}=.*$/${key}=${value}/" -i .env
    fi
}

function getEnvValue() {
    local key=$1
    local defaultValue=$2

    case ${key} in
        DOCKER_UID)
            value=$(id -u)
        ;;
        COMPOSE_PROJECT_NAME)
            value="$defaultValue"
        ;;
        *)
            if [ ! -f .env ] || [ "$(cat .env | grep -Ec "^${key}=(.*)$")" -eq 0 ]; then
                read -p "define the value of ${key} (default: ${defaultValue})" value
            else
                value=$(cat .env | grep -E "^${key}=(.*)$" | awk -F "${key} *= *" '{print $2}')
            fi
        ;;
    esac

    if [ "${value}" == "" ]; then
        value=${defaultValue}
    fi

    echo ${value}
}

for line in $(cat .env.dist)
do
    key=$(echo ${line} | awk -F "=" '{print $1}')
    defaultValue=$(echo ${line} | awk -F "${key} *= *" '{print $2}')
    configureEnv ${key} $(getEnvValue ${key} ${defaultValue})
done

docker-compose build
docker-compose up -d --remove-orphans

function block() {
    local titleLength=${#2}
    echo -en "\n\033[$1m\033[1;37m    "
    for x in $(seq 1 $titleLength); do echo -en " "; done;
    echo -en "\033[0m\n"

    echo -en "\033[$1m\033[1;37m  $2  \033[0m\n"
    echo -en "\033[$1m\033[1;37m    "
    for x in $(seq 1 $titleLength); do echo -en " "; done;
    echo -en "\033[0m\n\n"
}

. $DOCKER_PATH/.env
block 42 "Environment is started, you can go to http://$HTTP_HOST:$HTTP_PORT"
