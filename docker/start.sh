#!/usr/bin/env bash

set -e

readonly DOCKER_PATH=$(dirname $(realpath $0))
cd ${DOCKER_PATH};

. ./lib/functions.sh
. ./.env

#configure_env 'VERSION' "$(get_current_version)" '.env'

# Build all container in parallel to optimize your time
docker-compose build --parallel

# Start and remove useless containers
docker-compose up -d --remove-orphans
