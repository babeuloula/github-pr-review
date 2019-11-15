install-dev: create-env-dev start-dev composer-dev hooks

install-prod: create-env-prod start-prod composer-prod

create-env-dev:
	cp .env.dist .env.local

create-env-prod:
	cp .env.dist .env

start-dev: hooks
	bash ./docker/start_dev.sh

start-prod:
	bash ./docker/start_prod.sh

stop-dev:
	cd ./docker/ && docker-compose -f docker-compose.yml -f docker-compose.dev.yml stop

stop-prod:
	cd ./docker/ && docker-compose -f docker-compose.yml -f docker-compose.prod.yml stop

composer-dev:
	cd ./docker/ && docker-compose exec php composer global require hirak/prestissimo
	cd ./docker/ && docker-compose exec php composer install --no-interaction --no-progress

composer-prod:
	cd ./docker/ && docker-compose exec php composer global require hirak/prestissimo
	cd ./docker/ && docker-compose exec php composer install --no-dev --optimize-autoloader --no-interaction --no-progress

shell:
	cd ./docker/ && docker-compose exec php bash

check:
	cd ./docker/ && docker-compose exec -T php make phpcs
	cd ./docker/ && docker-compose exec -T php make stan
	cd ./docker/ && docker-compose exec -T php bin/console d:s:v
	cd ./docker/ && docker-compose exec -T php bin/console d:s:u --dump-sql

hooks:
	echo "#!/bin/bash" > .git/hooks/pre-commit
	echo "make check" >> .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit

phpcs:
	vendor/bin/phpcs

stan:
	bin/console cache:warmup --env=dev
	vendor/bin/phpstan analyse src --level max -c extension.neon
