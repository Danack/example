FROM debian:9

USER root

RUN apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y nginx \
    ca-certificates

WORKDIR /var/www

# Set up configuration volumes
RUN mkdir -p /etc/config

# Used by Jenkins to package code in to container
# COPY web /var/www

# CMD ["/usr/sbin/nginx", "-c", "/etc/nginx/nginx.conf"]

CMD ["/usr/sbin/nginx", "-c", "/var/www/docker/nginx/config/nginx.conf"]

# CMD ["tail", "-f", "/var/www/readme.MD"]