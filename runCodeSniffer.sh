#!/usr/bin/env bash

php vendor/bin/phpcs --standard=./test/codesniffer.xml --encoding=utf-8 --extensions=php -p -s lib