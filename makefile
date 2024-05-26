# Makefile

# APP
APP_DIR = app
APP_NAME = portfolio

# Executables (local)
DOCKER        = docker
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php
PHP_CONT_TEST = $(DOCKER_COMP) exec -e APP_ENV=test php
REDIS_CONT = $(DOCKER_COMP) exec redis

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console
REDIS    = $(REDIS_CONT)

# Executables: vendors
BEHAT_FLAGS ?=
BEHAT	 	  = $(PHP_CONT_TEST) ./vendor/bin/behat
PHPUNIT       = $(PHP_CONT) ./vendor/bin/phpunit
PHPSTAN       = $(PHP_CONT) ./vendor/bin/phpstan
PHP_CS_FIXER  = $(PHP_CONT) ./vendor/bin/php-cs-fixer

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop and remove all previous containers
	@$(DOCKER_COMP) down --remove-orphans
	@$(DOCKER) container prune -f
	@docker stop $(docker ps -a -q) || true
	@docker rm $(docker ps -a -q) || true
	@-$(DOCKER) volume rm $(docker volume -ls) -f

check: ## Docker check
	@$(DOCKER) info > /dev/null 2>&1                                                                   			# Docker is up
	@test '"healthy"' = `$(DOCKER) inspect --format "{{json .State.Health.Status }}" ${APP_NAME}-php-1` 		# php container is up and healthy
	@test '"healthy"' = `$(DOCKER) inspect --format "{{json .State.Health.Status }}" ${APP_NAME}-database-1` 	# Db container is up and healthy

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf


## —— Project 🐝 ———————————————————————————————————————————————————————————————
cc-redis: ## Flush all Redis cache
	@$(REDIS) -p 6389 flushall


## —— Tests ✅ —————————————————————————————————————————————————————————————————
test: check ## Run tests with optionnal suite and filter
	@$(eval testsuite ?= 'all')
	@$(eval filter ?= '.')
	@$(PHPUNIT) --testsuite=$(testsuite) --filter=$(filter) --stop-on-failure

behat:
	@APP_ENV=test $(BEHAT) --colors --stop-on-failure $(BEHAT_FLAGS)


## —— Coding standards ✨ ——————————————————————————————————————————————————————
cs: fix-php stan ## Run all coding standards checks

static-analysis: stan ## Run the static analysis (PHPStan)

stan: ## Run PHPStan
	@$(PHPSTAN) analyse -c phpstan.dist.neon --memory-limit 1G

lint-php: ## Lint files with php-cs-fixer
	@$(PHP_CS_FIXER) fix --allow-risky=yes --dry-run

fix-php: ## Fix files with php-cs-fixer
	@PHP_CS_FIXER_IGNORE_ENV=1 $(PHP_CS_FIXER) fix --allow-risky=yes
