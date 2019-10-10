install: start composer hooks

start: hooks
	bash ./docker/start.sh

stop:
	cd ./docker/ && docker-compose stop

composer:
	cd ./docker/ && docker-compose exec php composer global require hirak/prestissimo
	cd ./docker/ && docker-compose exec php composer install --no-interaction --no-progress

shell:
	cd ./docker/ && docker-compose exec php bash

check:
	cd ./docker/ && docker-compose exec -T php make phpcs
	cd ./docker/ && docker-compose exec -T php make stan

hooks:
	echo "#!/bin/bash" > .git/hooks/pre-commit
	echo "make check" >> .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit

phpcs:
	vendor/bin/phpcs

stan:
	bin/console cache:warmup --env=dev
	vendor/bin/phpstan analyse src --level max -c extension.neon
