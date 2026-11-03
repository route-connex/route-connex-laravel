# Docker-based development helpers: no local PHP needed.
export UID := $(shell id -u)
export GID := $(shell id -g)

RUN := docker compose run --rm app
# Pest against one database, extra arguments via ARGS: $(call pest_on,mysql)
pest_on = docker compose run --rm -e DB_CONNECTION=$(1) app vendor/bin/pest $(ARGS)
DB_SERVICES := mysql pgsql mongodb

.PHONY: help build install update composer test test-unit lint analyse serve testbench shell \
	db-up db-down test-sqlite test-mysql test-pgsql test-mongodb test-all

help: ## List available targets
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-14s\033[0m %s\n", $$1, $$2}'

build: ## Build the PHP image
	docker compose build app

install: ## composer install
	$(RUN) composer install

update: ## composer update
	$(RUN) composer update

composer: ## Run any composer command: make composer ARGS="require foo/bar"
	$(RUN) composer $(ARGS)

test: ## Full suite (PHPStan, Pint, type coverage, Pest) on SQLite
	docker compose run --rm -e DB_CONNECTION=sqlite app composer test

test-unit: ## Pest only, on SQLite: make test-unit ARGS="--filter=Foo"
	docker compose run --rm -e DB_CONNECTION=sqlite app composer test:unit -- $(ARGS)

lint: ## Fix code style with Pint
	$(RUN) composer lint

analyse: ## Static analysis with PHPStan/Larastan
	$(RUN) composer analyse

serve: ## Workbench app on http://localhost:8000
	docker compose run --rm --service-ports app composer serve

testbench: ## Artisan for the package: make testbench ARGS="route:list"
	$(RUN) vendor/bin/testbench $(ARGS)

shell: ## Shell inside the PHP container
	$(RUN) bash

db-up: ## Start MySQL, PostgreSQL and MongoDB
	docker compose --profile databases up -d --wait $(DB_SERVICES)

db-down: ## Stop and remove the database containers (and their anonymous volumes)
	docker compose --profile databases rm --force --stop --volumes $(DB_SERVICES)

test-sqlite: ## Pest against SQLite (accepts ARGS)
	$(call pest_on,sqlite)

test-mysql: ## Pest against MySQL (accepts ARGS)
	docker compose --profile databases up -d --wait mysql
	$(call pest_on,mysql)

test-pgsql: ## Pest against PostgreSQL (accepts ARGS)
	docker compose --profile databases up -d --wait pgsql
	$(call pest_on,pgsql)

test-mongodb: ## Pest against MongoDB (accepts ARGS)
	docker compose --profile databases up -d --wait mongodb
	$(call pest_on,mongodb)

test-all: test-sqlite test-mysql test-pgsql test-mongodb ## Pest against all four databases
