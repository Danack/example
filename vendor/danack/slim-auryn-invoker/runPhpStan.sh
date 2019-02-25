#!/usr/bin/env bash

set -e
set -x

# docker-compose exec -T php_fpm sh -c "php phpstan.phar analyze -c ./phpstan.neon -l 7 lib"

php phpstan.phar analyze -vvv -c ./phpstan.neon -l 7 lib example