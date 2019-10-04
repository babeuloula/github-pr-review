install: start composer

start:
	bash ./docker/start.sh

stop:
	cd ./docker/ && docker-compose stop

composer:
	cd ./docker/ && docker-compose exec php composer global require hirak/prestissimo
	cd ./docker/ && docker-compose exec php composer install --no-interaction --no-progress

shell:
	cd ./docker/ && docker-compose exec php bash

check:
	cd ./docker/ && docker-compose exec php make phpcs
	cd ./docker/ && docker-compose exec php make stan

phpcs:
	vendor/bin/phpcs

stan:
	bin/console cache:warmup --env=dev
	vendor/bin/phpstan analyse src --level 7
