APP_ENV ?= dev
CONTAINER_NAME ?= php
DOCKER_COMPOSE = docker-compose --project-name snowtricks --project-directory .
OS := $(shell uname)

##
## # Install
##---------------------------------------------------------------------------
.PHONY: install install-prod

install: ## Install project in dev environment
install: export APP_ENV=dev
install: dc-up composer-install db-reset

install-prod: ## Install project in prod environment
install-prod: export APP_ENV=prod
install-prod: dc-prod composer-install

##
## # Docker Compose
##---------------------------------------------------------------------------
.PHONY: dc-build dc-down dc-exec dc-logs dc-ps dc-start dc-stop dc-up

dc-build: ## Build containers
dc-build: .env
	$(DOCKER_COMPOSE) build

dc-down: ## Remove containers and delete volumes
	$(DOCKER_COMPOSE) down --remove-orphans --volumes

dc-exec: CONTAINER_NAME := $(or $(word 2, $(MAKECMDGOALS)), $(CONTAINER_NAME))
dc-exec: ## Execute a command in the application container
	$(DOCKER_COMPOSE) exec $(CONTAINER_NAME) sh

dc-logs: CONTAINER_NAME := $(or $(word 2, $(MAKECMDGOALS)), $(CONTAINER_NAME))
dc-logs: ## Show container logs
	$(DOCKER_COMPOSE) logs -f $(CONTAINER_NAME)

dc-prod: ## Up containers in prod environment
	$(DOCKER_COMPOSE) -f compose.yaml -f compose.prod.yaml up --wait

dc-ps: ## Show running containers
	$(DOCKER_COMPOSE) ps

dc-start: ## Start docker containers
	$(DOCKER_COMPOSE) start

dc-stop: ## Stop docker containers
	$(DOCKER_COMPOSE) stop

dc-trust-certificate:
		@if [ "$(OS)" = "Linux" ]; then \
			docker cp $$($(DOCKER_COMPOSE) ps -q php):/data/caddy/pki/authorities/local/root.crt /usr/local/share/ca-certificates/root.crt && sudo update-ca-certificates; \
		elif [ "$(OS)" = "Darwin" ]; then \
			docker cp $$($(DOCKER_COMPOSE) ps -q php):/data/caddy/pki/authorities/local/root.crt /tmp/root.crt && sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /tmp/root.crt; \
		else \
			docker compose cp php:/data/caddy/pki/authorities/local/root.crt %TEMP%/root.crt && certutil -addstore -f "ROOT" %TEMP%/root.crt; \
		fi

dc-up: ## Up containers
dc-up: .env
	$(DOCKER_COMPOSE) up -d

##
## # Database
##---------------------------------------------------------------------------
.PHONY: db-create db-diff db-drop db-migrations db-reset

db-create: ## Create Database
	$(DOCKER_COMPOSE) exec php php bin/console doctrine:database:create --if-not-exists --env=$(APP_ENV)

db-diff: ## Doctrine diff
	$(DOCKER_COMPOSE) exec php php bin/console doctrine:migrations:diff --env=$(APP_ENV)

db-drop: ## Drop database
	$(DOCKER_COMPOSE) exec php php bin/console doctrine:database:drop --if-exists --force --env=$(APP_ENV)

db-fixtures: ## Load fixtures
	$(DOCKER_COMPOSE) exec php php bin/console doctrine:fixtures:load --no-interaction --env=$(APP_ENV)

db-migrations: ## Execute Doctrine migrations
	$(DOCKER_COMPOSE) exec php php bin/console doctrine:migrations:migrate --no-interaction --env=$(APP_ENV)

db-reset: ## Reset Database
db-reset: db-drop db-create db-migrations db-fixtures

##
## # Composer
##---------------------------------------------------------------------------
.PHONY: composer-install

composer-install: ## Install composer dependencies
	$(if $(filter $(APP_ENV), dev or test),\
		$(DOCKER_COMPOSE) exec php composer install,\
		$(DOCKER_COMPOSE) exec php composer dump-env prod && \
		$(DOCKER_COMPOSE) exec php composer install --no-dev --optimize-autoloader \
	)

##
## # Symfony
##---------------------------------------------------------------------------
.PHONY: sf

sf: ## Symfony command example: make sf c='c:c -e prod'
	@$(eval c ?=)
	$(DOCKER_COMPOSE) exec php php bin/console $(c)

##
## # Checks
##---------------------------------------------------------------------------
.PHONY: cs

cs: ## Run php-cs-fixer
	$(DOCKER_COMPOSE) exec php vendor/bin/php-cs-fixer fix --diff --verbose

phpstan: ## Run phpstan
	$(DOCKER_COMPOSE) exec php vendor/bin/phpstan

rector: ## Run rector
	$(DOCKER_COMPOSE) exec php vendor/bin/rector

##
## # Tests
##---------------------------------------------------------------------------
.PHONY: tests tests-reset

tests: ## Run tests
tests: export APP_ENV=test
tests:
	$(DOCKER_COMPOSE) exec php php bin/phpunit

tests-reset: ## Recreate database, launch migrations, load fixtures and execute tests
tests-reset: export APP_ENV=test
tests-reset: db-reset tests

##
## # Help
##---------------------------------------------------------------------------
.PHONY: help

help: ## Display help
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

.DEFAULT_GOAL := 	help
