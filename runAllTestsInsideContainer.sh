#!/usr/bin/env bash

set -e

docker-compose exec -T php_fpm sh -c "sh runAllTests.sh"


