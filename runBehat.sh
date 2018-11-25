#!/usr/bin/env bash

set -e

# Run all tests

docker-compose exec -T php_fpm sh -c "php vendor/bin/behat --config=./behat.yml --colors --stop-on-failure"



# Run the tests that are currently being worked on
# docker-compose exec php_fpm sh -c "php vendor/bin/behat --config=./behat.yml --colors --stop-on-failure --tags"

