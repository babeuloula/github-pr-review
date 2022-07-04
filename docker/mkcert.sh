#!/usr/bin/env bash

set -e

readonly DOCKER_PATH=$(dirname $(realpath $0))
cd ${DOCKER_PATH};

. ./lib/functions.sh
. ./.env

block_info "SSL certificates generation"

sudo apt install libnss3-tools ca-certificates wget -y

if [[ -z $(which mkcert) ]]; then
    echo -e "${GREEN}mkcert is not installed.${RESET}" > /dev/tty

    readonly MKCERT_VERSION=1.4.3
    wget "https://github.com/FiloSottile/mkcert/releases/download/v${MKCERT_VERSION}/mkcert-v${MKCERT_VERSION}-linux-amd64"
    sudo mv "mkcert-v${MKCERT_VERSION}-linux-amd64" /usr/bin/mkcert
    sudo chmod +x /usr/bin/mkcert
fi

readonly CERT_PATH="$(pwd)/certificates"

rm -f ${CERT_PATH}/*.key
rm -f ${CERT_PATH}/*.pem

mkcert -cert-file "${CERT_PATH}/${HTTP_HOST}.pem" -key-file "${CERT_PATH}/${HTTP_HOST}.key" ${HTTP_HOST}
mkcert -install

echo -e "${GREEN}SSL certificates generation done!${RESET}" > /dev/tty
