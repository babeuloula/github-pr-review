install: start composer

start:
	bash ./docker/start.sh

composer:
	cd ./docker/ && docker-compose exec -T php composer global require hirak/prestissimo
	cd ./docker/ && docker-compose exec -T php composer install --ignore-platform-reqs --no-interaction --no-progress --classmap-authoritative

shell:
	cd ./docker/ && docker-compose exec php bash

check:
	cd ./docker/ && docker-compose exec -T php make phpcs
	cd ./docker/ && docker-compose exec -T php make stan

phpcs:
	vendor/bin/phpcs

stan:
	bin/console cache:warmup --env=dev
	vendor/bin/phpstan analyse src --level 7

