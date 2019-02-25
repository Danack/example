FROM slimauryn_php_fpm:latest

RUN DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends php7.2-xdebug

COPY xdebug.ini /etc/php/7.2/fpm/conf.d/20-xdebug.ini
