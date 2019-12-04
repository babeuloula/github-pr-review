#!/usr/bin/env bash

set -e

# PROMPT COLOURS
readonly RESET='\033[0;0m'
readonly BLACK='\033[0;30m'
readonly RED='\033[0;31m'
readonly GREEN='\033[0;32m'
readonly YELLOW='\033[0;33m'
readonly BLUE='\033[0;34m'
readonly PURPLE='\033[0;35m'
readonly CYAN='\033[0;36m'
readonly WHITE='\033[0;37m'

readonly DOCKER_MINIMAL_VERSION=18.04.0
readonly DOCKER_COMPOSE_MINIMAL_VERSION=1.24.0

function checkDocker() {
    if [[ "$(which docker)" == "" ]]; then
        echo -e "${RED}Requirement: need 'docker:${DOCKER_MINIMAL_VERSION}' see https://docs.docker.com/install/linux/docker-ce/ubuntu .${RESET}" > /dev/tty
        exit 1
    fi

    checkVersion $(docker -v | sed -r 's/.* version ([^,]+),.*/\1/') ${DOCKER_MINIMAL_VERSION} 'docker'
}

function checkDockerCompose() {
    if [[ "$(which docker-compose)" == "" ]]; then
        echo -e "${RED}Requirement: need 'docker-compose:${DOCKER_COMPOSE_MINIMAL_VERSION}' see https://docs.docker.com/compose/install .${RESET}" > /dev/tty
        exit 1
    fi

    checkVersion $(docker-compose -v | sed -r 's/.* version ([^,]+),.*/\1/') ${DOCKER_COMPOSE_MINIMAL_VERSION} 'docker-compose'
}

function checkVersion() {
    local version=$1
    local requireVersion=$2
    local package=$3

    dpkg --compare-versions ${version} 'ge' ${requireVersion} \
        || (echo -e "${RED}Requirement: need '${package}:${requireVersion}', you have '${package}:${version}'.${RESET}" > /dev/tty && exit 1)
}

function checkRequirements() {
    checkDocker
    checkDockerCompose
}

function addHost() {
    local host=$1
    if [ "$(cat /etc/hosts | grep -c ${host})" -eq 0 ]; then
        sudo /bin/sh -c "echo \"127.0.0.1 ${host}\" >> /etc/hosts"
    fi
}

function configureEnv() {
    local key=$1
    local value=$2

    if [ -f .env ]; then
        sed -e "/^${key}=/d" -i .env
    fi

    echo "${key}=${value}" >> .env
}

function getEnvValue() {
    local key=$1
    local defaultValue=$2

    case ${key} in
        DOCKER_UID)
            value=$(id -u)
        ;;
        COMPOSE_PROJECT_NAME)
            value=${defaultValue}
        ;;
        *)
            if [ ! -f .env ] || [ "$(cat .env | grep -Ec "^${key}=(.*)$")" -eq 0 ]; then
                read -p "define the value of ${key} (default: ${defaultValue}): " value
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

function dockerStart() {
    local env=$1

    docker-compose -f "docker-compose.yml" -f "docker-compose.${env}.yml" build --parallel
    docker-compose -f "docker-compose.yml" -f "docker-compose.${env}.yml" up -d --remove-orphans
}

function installComposer() {
    local env=$1

    docker-compose exec php composer global require hirak/prestissimo

    if [ "${env}" == "dev" ]; then
        docker-compose exec php composer install --no-interaction --no-progress
    else
        docker-compose exec php composer install --no-dev --optimize-autoloader --no-interaction --no-progress --classmap-authoritative
    fi
}

function waitMysql() {
    echo -e "${BLUE}Wait for MySQL...${RESET}" > /dev/tty

    maxcounter=45
    counter=1
    while ! docker-compose exec mysql mysql --protocol TCP -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" -e "show databases;" > /dev/null 2>&1; do
        sleep 1
        counter=`expr $counter + 1`
        if [ $counter -gt $maxcounter ]; then
            >&2 echo -e "${RED}We have been waiting for MySQL too long already; failing.${RESET}" > /dev/tty
            exit 1
        fi;
    done
}

function databaseAndMigrations() {
    docker-compose exec php bin/console doctrine:database:create --if-not-exists
    docker-compose exec php bin/console doctrine:migration:migrate --allow-no-migration --no-interaction
}

function getCurrentVersion() {
    gitVersion=$(git for-each-ref refs/tags --sort=-taggerdate --format='%(refname)' --count=1)

    echo "$gitVersion" | sed -r 's/refs\/tags\/v//g'
}
