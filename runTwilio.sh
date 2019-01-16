#!/usr/bin/env bash


docker-compose build
docker-compose -f docker-compose.yml -f docker-secrets.yml up twilio

# docker-compose exec -T php_fpm sh -c "php phpstan.phar analyze -c ./phpstan.neon -l 7 lib"



