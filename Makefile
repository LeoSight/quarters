APP_ENV ?= dev
BUILD_ENV ?= local
DOCKER_COMPOSE_EXTEND_CONFIG = docker-compose.${BUILD_ENV}.yml

docker_compose = docker-compose -f docker-compose.yml
app_folder = /var/www/php-application/
get_container = $(firstword $(container) php)
php_container = php

buildcontainer:
	$(docker_compose) stop
	APP_ENV=${APP_ENV} BUILD_ENV=${BUILD_ENV} $(docker_compose) -f ${DOCKER_COMPOSE_EXTEND_CONFIG} build $(container)
	$(docker_compose) stop

install: buildcontainer

setuphook:
	cp hooks/pre-push .git/hooks && chmod +x .git/hooks/pre-push && cp hooks/pre-commit .git/hooks && chmod +x .git/hooks/pre-commit

test: up
	$(docker_compose) exec -T $(php_container) bash -c '(cd $(app_folder) && composer test-full)'

fix: up
	$(docker_compose) exec -T $(php_container) bash -c '(cd $(app_folder) && composer fix-all)'

commit:
	npx --package cz-emoji-conventional --package commitizen -- cz

check: up
	$(docker_compose) exec -T $(php_container) bash -c '(cd $(app_folder) && composer check-all)'

logs:
	$(docker_compose) logs $(get_container)

up: setuphook
	APP_ENV=${APP_ENV} BUILD_ENV=${BUILD_ENV} $(docker_compose) -f ${DOCKER_COMPOSE_EXTEND_CONFIG} up -d $(container) --remove-orphans

stop:
	$(docker_compose) stop $(container)

down:
	$(docker_compose) down $(container)

bashroot:
	$(docker_compose) exec $(get_container) bash -c 'cd $(app_folder) && bash'

updatedatabase: up
	$(docker_compose) exec $(php_container) bash -c '($(app_folder)/bin/console doctrine:database:create --if-not-exists \
	&& $(app_folder)/bin/console doctrine:schema:update --complete --quiet --force)'

dumpautoload:
	$(docker_compose) exec -T $(php_container) composer dump-autoload --no-interaction --working-dir="/var/www/php-application"
