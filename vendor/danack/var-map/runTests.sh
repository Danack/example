#!/usr/bin/env bash

set -e
set -x

php vendor/bin/phpcs --standard=./test/codesniffer.xml --encoding=utf-8 --extensions=php -p -s lib

php vendor/bin/phpunit -c test/phpunit.xml

php phpstan.phar analyze -c ./phpstan.neon -l 7 lib

set +e

## skip infection for now.
# php vendor/bin/infection --log-verbosity=0 --only-covered --min-covered-msi=100
# infection_exit_code=$?
# set -e
#
# if [ "$infection_exit_code" -ne "0" ]; then echo "Infection failed"; cat infection-log.txt;  exit "$infection_exit_code"; fi

echo "Tests completed without problem"

# rerun unit tests to get the stats again, to save scrolling...
php vendor/bin/phpunit -c test/phpunit.xml
