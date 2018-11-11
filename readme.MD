

# Example box

This is an example docker box for a Slim application using Auryn. 

There is quite a bit of stuff I'm going to add to it, so currently these instructions are placeholder.



## Quickstart


1. Get Docker community edition and start it

2. Allow file mounts to work

* Click the whale icon for docker at the edge of your screen. 
* Under 'File Sharing' add a path that you will check the project out into e.g /projects


3. Checkout the project and start it.

```
git clone https://github.com/Danack/example
cd example
docker-compose build
docker-compose up --build --abort-on-container-exit

```

Wait for text to stop scrolling by...

4. Go to http://local.app.basereality.com/ in your browser.


## Docker commands

After checking out the repo, building the images

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
docker exec -it example_web_admin_1 bash
```



# Top level concepts


## Environments

* App - the actual application itself. 
* Admin - the admin site that administers the application.
* Api - api endpoints for use with JS or external calls.
* Cli - all of the command line tools that are used in the background.


## Routes

All of the routes are in routes directory.

## Injection parameters

All of the injection parameters are in the injectionParams directory. There should be one file per environment plus other files for testing.

# Other stuff.



## Loopback address - how to setup

Copy the file ./docker/com.ralphschindler.docker_10254_alias.plist to /Library/LaunchDaemons/com.ralphschindler.docker_10254_alias.plist
to create a nice loopback address.

This allows the same IP address for the MySQL database to work both inside and outside the box.





