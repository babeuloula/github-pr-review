#!/usr/bin/env bash

set -e

readonly DOCKER_PATH=$(dirname $(realpath $0))
cd ${DOCKER_PATH};

. ./lib/functions.sh

block_info "Welcome to Github PR Review installer!"

check_requirements
parse_env ".env.dist" ".env"
. ./.env
rm "docker-compose.override.yml" || true
cp "docker-compose.override.${DOCKER_ENV}.yml" "docker-compose.override.yml"
echo -e "${GREEN}Configuration done!${RESET}" > /dev/tty

if [[ "${DOCKER_ENV}" == "dev" ]]; then
    # Install SSL certificates for dev
    ./mkcert.sh

    add_host "${HTTP_HOST}"
fi

block_info "Build & start Docker"
# Pull all container in parallel to optimize your time
docker-compose pull
./stop.sh
./start.sh
echo -e "${GREEN}Docker is started with success!${RESET}" > /dev/tty

block_info "Install dependencies"
install_composer "${DOCKER_ENV}"
echo -e "${GREEN}Dependencies installed with success!${RESET}" > /dev/tty

wait_database
database_and_migrations

block_success "Github PR Review is started."

