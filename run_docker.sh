#!/usr/bin/env bash

docker-compose -f docker-compose.yml -f docker-compose.dev.yml build --force-rm --no-cache

docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d --force-recreate --remove-orphans -V
docker-compose -f docker-compose.yml -f docker-compose.dev.yml exec php bash
