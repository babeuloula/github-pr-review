#-include docker/.env

.SILENT: shell analyse clear-cache
.DEFAULT_GOAL := help

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

##
## Project
##---------------------------------------------------------------------------

install: ## Install the project
install: hooks
	cd ./docker && ./install.sh

start: ## Start the project
start: hooks
	cd ./docker && ./start.sh

stop: ## Stop the project
stop:
	cd ./docker && ./stop.sh

restart: ## Restart the project
restart: stop start

update: ## Update the project
update:
	cd ./docker && ./update.sh

hooks:
	# Pre commit
	echo "#!/bin/bash" > .git/hooks/pre-commit
	echo "make check" >> .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit

shell: ## Connect to PHP container
shell:
	cd ./docker && docker-compose exec php bash

warmup-cache:
	bin/console cache:warmup

clear-cache:
	bin/console cache:clear --no-warmup

##
## Database
##---------------------------------------------------------------------------

reset: ## Reset the database (only on container)
reset:
	bin/console doctrine:database:drop --if-exists --force
	make doctrine-migrations

doctrine-migrations: ## Execute all migrations (only on container)
doctrine-migrations:
	bin/console doctrine:database:create --if-not-exists
	bin/console doctrine:migration:migrate --allow-no-migration --no-interaction --all-or-nothing

##
## Code quality (only on PHP test container)
##---------------------------------------------------------------------------

check:
	cd ./docker/ && docker-compose exec -T php make clear-cache
	cd ./docker/ && docker-compose exec -T php make lint
	cd ./docker/ && docker-compose exec -T php make analyse
	cd ./docker/ && docker-compose exec -T php make copy-paste
	cd ./docker/ && docker-compose exec -T php make doctrine
	cd ./docker/ && docker-compose exec -T php make security

lint: ## Execute PHPCS
lint:
	vendor/bin/phpcs -p

fixer: ## Execute PHPCS fixer
fixer:
	./vendor/bin/phpcbf -p

analyse: ## Execute PHPStan
analyse:
	bin/console cache:warmup --env=dev
	vendor/bin/phpstan analyse --memory-limit=4G

doctrine: ## Validate Doctrine schema
doctrine: reset
	bin/console d:s:v --env=test
	bin/console d:s:u --dump-sql --env=test

copy-paste: ## Check duplicate code
copy-paste:
	./bin/phpcpd src \
		--fuzzy

security: ## Check CVE for vendor dependencies
security:
	./bin/security-checker --path=./composer.lock
