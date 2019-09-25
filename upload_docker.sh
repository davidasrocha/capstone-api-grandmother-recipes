#!/usr/bin/env bash

docker tag davidasrocha/api-grandmother-recipes-nginx davidasrocha/api-grandmother-recipes-nginx:latest

docker tag davidasrocha/api-grandmother-recipes-php davidasrocha/api-grandmother-recipes-php:latest

docker push davidasrocha/api-grandmother-recipes-nginx:latest

docker push davidasrocha/api-grandmother-recipes-php:latest
