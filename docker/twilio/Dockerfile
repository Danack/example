FROM debian:9

#LABEL version="1.0.0"
#LABEL repository="https://github.com/helaili/jekyll-action"
#LABEL homepage="https://github.com/helaili/jekyll-action"
#LABEL maintainer="Alain Hélaïli <helaili@github.com>"
#
#LABEL "com.github.actions.name"="Jekyll Action"
#LABEL "com.github.actions.description"="A GitHub Action to build and publish Jekyll sites to GitHub Pages"
#LABEL "com.github.actions.icon"="book"
#LABEL "com.github.actions.color"="blue"
#COPY LICENSE README.md /

RUN apt-get update -qq

RUN apt-get install -y curl

USER root

WORKDIR /var/app

RUN mkdir -p /var/app

COPY entryPoint_twilio.sh /var/app/entryPoint_twilio.sh

CMD ["sh", "/var/app/entryPoint_twilio.sh"]

# CMD whoami && sh /var/app/entryPoint_twilio.sh
#CMD tail -f /var/app/entryPoint_twilio.sh


