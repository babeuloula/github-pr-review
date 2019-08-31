#!/usr/bin/env sh

set -e

readonly PROJECT_DIRECTORY=$(dirname $(realpath $0)))

. "$PROJECT_DIRECTORY/.env";
if [ -f "$PROJECT_DIRECTORY/.env.local" ]; then
    . "$PROJECT_DIRECTORY/.env.local";
fi

if [ "$APP_ENV" = "prod" ]; then
    composer install --no-dev --classmap-authoritative
    composer dump-env prod
else
    composer install
    if [ -f "$PROJECT_DIRECTORY/.env.local.php" ]; then
        rm "$PROJECT_DIRECTORY/.env.local.php"
    fi
fi

bin/console cache:clear
bin/console assets:install
