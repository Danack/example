# VarMap

A very simple library to hold a 'Variable Map' interface and implementations.


## Tests

We have several tools that are run to improve code quality.

Standard unit tests:

```
php vendor/bin/phpunit -c test/phpunit.xml
```


Code sniffer for code styling.

```
php vendor/bin/phpcs --standard=./test/codesniffer.xml --encoding=utf-8 --extensions=php -p -s lib

```


```
php phpstan.phar analyze -c ./phpstan.neon -l 4 lib
```