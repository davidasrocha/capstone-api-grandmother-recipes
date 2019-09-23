#!/usr/bin/env bash

docker-compose -f docker-compose.yml -f docker-compose.dev.yml build --force-rm --no-cache

docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d --force-recreate --remove-orphans -V

docker-compose -f docker-compose.yml -f docker-compose.dev.yml exec php composer install -o

docker-compose -f docker-compose.yml -f docker-compose.dev.yml exec php bin/console cache:clear
docker-compose -f docker-compose.yml -f docker-compose.dev.yml exec php bin/console cache:warmup

docker-compose -f docker-compose.yml -f docker-compose.dev.yml exec php bin/console doctrine:schema:create

docker-compose -f docker-compose.yml -f docker-compose.dev.yml exec php bash
