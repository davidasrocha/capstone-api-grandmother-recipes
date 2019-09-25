#!/usr/bin/env bash

docker-compose down --remove-orphans -v

docker-compose rm -f -s -v

docker-compose build --force-rm --no-cache
