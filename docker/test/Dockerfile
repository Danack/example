FROM debian:9

USER root

# Get Debian up-to-date
RUN apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y git \
    mariadb-client wget curl \
    ca-certificates lsb-release apt-transport-https gnupg bsdmainutils

# Install 3rd party PHP 7.2 packages
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee -a /etc/apt/sources.list.d/php.list
RUN curl -L https://packages.sury.org/php/apt.gpg | apt-key add -

RUN  apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y php7.2 php7.2-common php7.2-cli php7.2-fpm \
    php7.2-mysql  php7.2-curl php7.2-xml php7.2-mbstring \
    php7.2-intl php7.2-redis php7.2-zip

# WORKDIR /var/app

WORKDIR /github/workspace

CMD ["sh", "runAllTests.sh"]

# CMD ls -l && pwd && echo $(find . -name readme_xdebug.md -exec dirname {} \;) && echo "okay"

