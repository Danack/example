#!/usr/bin/env bash

# Run all tests

docker-compose exec php_backend sh -c "php vendor/bin/behat --config=./behat.yml --colors --stop-on-failure"



# Run the tests that are currently being worked on
# docker-compose exec php_backend sh -c "php vendor/bin/behat --config=./behat.yml --colors --stop-on-failure --tags"

