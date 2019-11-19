start-dev: hooks
	bash ./docker/start_dev.sh

start-prod:
	bash ./docker/start_prod.sh

stop-dev:
	cd ./docker/ && docker-compose -f docker-compose.yml -f docker-compose.dev.yml stop

stop-prod:
	cd ./docker/ && docker-compose -f docker-compose.yml -f docker-compose.prod.yml stop

shell:
	cd ./docker/ && docker-compose exec php bash

check:
	cd ./docker/ && docker-compose exec -T php make phpcs
	cd ./docker/ && docker-compose exec -T php make stan
	cd ./docker/ && docker-compose exec -T php make check-doctrine

hooks:
	echo "#!/bin/bash" > .git/hooks/pre-commit
	echo "make check" >> .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit

phpcs:
	vendor/bin/phpcs

stan:
	bin/console cache:warmup --env=dev
	vendor/bin/phpstan analyse src --level max -c extension.neon

check-doctrine:
	bin/console d:s:v
	bin/console d:s:u --dump-sql
