#!/usr/bin/env bash

docker-compose exec php_backend sh -c "php phpstan.phar analyze -c ./phpstan.neon -l 7 lib"

# php phpstan.phar analyze -c ./phpstan.neon -l 6 lib