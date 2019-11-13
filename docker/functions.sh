#!/usr/bin/env bash

set -e

function addHost() {
    local host=$1
    if [ "$(cat /etc/hosts | grep -c ${host})" -eq 0 ]; then
        sudo /bin/sh -c "echo \"127.0.0.1 ${host}\" >> /etc/hosts"
    fi
}

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
