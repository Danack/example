
# Docker example

Docker, or more precisely docker-compose, is a great tool for local development. 

It allows for easy to maintain, reliable local development environment that is a close match for the production environment.

This example project contains an example of a docker-compose setup that would reflect a real world (i.e. not just 'hello world') application.


## How Docker-compose works


The information needed for Docker Compose to work is held in a couple of different files:

* docker-compose.yml in the root of the project. This file defines the containers that are built when docker-compose is run

* A Dockerfile in each of the directories under ./example/docker there is a Dockerfile that contains the instructions for how to build that container. e.g. ./example/docker/php_fpm/Dockerfile contains the instructions for building the php_backend container.

Note not all of the containers this project uses have Dockerfiles. For example the 'db' container builds directly from a container named 'mysql:5.7' that is pulled from Docker hub.

* Config files. All of the config files needed to run the containers are inside the appropriate directory under ./example/docker.


## Docker compose file

The docker compose defines the containers (or services) that 


## Dockerfiles

Dockerfiles contain the instructions of how to actually build a container.

Each step in a dockerfile builds from the container state of the previous step.

```
# Starting from the official Debian 9 container 
FROM debian:9

# Make sure we're doing things as root
USER root

# Update Debian and install nginx, and some certificates
RUN apt-get update -qq \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y nginx \
    ca-certificates

# Make the working directory for the container to be where our app is
WORKDIR /var/www

# Run nginx and watch the output
CMD ["/usr/sbin/nginx", "-c", "/var/www/docker/nginx/config/nginx.conf"]
```



## The containers

This is a brief description of what each of the containers does, and notes on how they work.

### db

This runs MySQL in a container. All of the data files for MySQL in this project are held on the host system, so that the data inside the MySQL database persists across the containers being destroyed and recreated. 

The directory that holds the MySQL data is ./example/data/mysql. If for any reason you want to start from a clean empty database, this can be accomplished safely by:

* Stopping the containers.
* Deleting the complete contents of ./example/data/mysql
* Bringing the containers back up.

All of the relevant files will be created on startup by the db container.


Additionally, if you wish to restore some data to the database (e.g. to seed the DB with example data) this can be accompished by putting .sql or .sql.gz files into the directory ./example/data/mysql_import.

Any files with either .sql or .sql.gz extensions will be automatically imported into the DB, the first time that the container is brought up.


### php_fpm

It's a container that runs PHP-FPM so that nginx can send it web-requests.

By adding port 8000 (e.g. http://local.app.basereality.com:8000) requests will be sent to this container, skipping Varnish.

### php_fpm_debug

This is the same as the php_fpm container, except that it also has xdebug enabled, so that programmers can debug problems straight away.

By adding port 8001 (e.g. http://local.app.basereality.com:8001) requests will be sent to this container, skipping Varnish.

### nginx

It's nginx. It passes requests to the PHP backends.

### redis

It's Redis running in a container. \o/
    
### selenium-chrome

This container runs both [Selenium](https://www.seleniumhq.org/) and Google Chrome in a single container, to allow integration tests to be run. 

The image used is from [Docker hub](https://hub.docker.com/r/selenium/standalone-chrome-debug/) and so there is no Dockerfile for this container in this project. 

Multiple options are passed to the selenium-chrome container from the docker-compose.yml file, to set options including the screen width and height.

Please read the [readme_behat.md](readme_behat.md) file to to learn more about integration tests.


### supervisord

Supervisord is a program that manages running other programs. For each program you want to run, you specify the command that should be used to invoke the program, and how many instances of that program you want to be running at the same time.

Supervisord will start those programs, make sure they are running and restart any that fall over, or quit after a reasonable amount of time.

Additionally, Supervisor provides a dashboard page where you can see the status of the programs that are being run which is exposed in this project at http://127.0.0.1:8080/

### varnish

Currently disabled.


## Docker commands

After checking out the repo, build the images

```
docker-compose build
```

After that has been run once, you should be able to re-build the containers and bring them up using.

```
docker-compose up --build --abort-on-container-exit
```

This will leave the containers running, with the output of the containers being sent to the screen. 

The containers can be stopped cleanly (most of the time) by pressing ctrl+C. Pressing ctrl+C again while they are shutting down should hard kill those containers.

The `--build` flag _technically_ isn't required. In practice, it seems to avoid certain bugs or scenarios where docker is unable to bring the boxes up correctly.


Sometimes the containers don't stop after pressing ctrl+C. They can be shut harder with:
```
docker-compose down
```


Very, very occasionally, something may go wrong with the docker boxes, or you might want to reclaim some of the hard disk space used by docker container images. The following commands should be run to destroy all boxes.

```
docker rm $(docker ps -a -q)
docker rmi $(docker images -q)
docker network rm $(docker network ls -q)
```

You can view details, including names, of the running docker containers with:
```
docker ps
```

You can bash into a running box with:

```
docker-compose exec -T php_fpm bash
```

```
docker ps
# Find the name of the container you want to bash into
# and copy it to be used like this:
docker exec -it example_php_fpm_1_3a95b3721bd0 bash
```

