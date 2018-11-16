
# Xdebug example


Having xdebug available immediately is a vital tool for debugging code. If developers need to restart vagrant or docker boxes to be able to debug problems, they will naturally fall back to 'var_dump debugging' which is a terribly inefficient way of figuring out what code is doing.

This example project is setup with two PHP backend containers which are almost identical. One of them, named 'php_backend' doesn't have xdebug enabled. The other, name 'php_backend_debug' always has xdebug enabled.

The xdebug enabled PHP backend can be reached directly by specifying port 8001.

e.g. http://local.app.basereality.com:8001/


The recommended work flow is to usually use the non-xdebug container when working (i.e. make requests to the domain name without the port specifiec -  http://local.app.basereality.com/) and then when a problem is encountered that needs some debugging, add port 8001 to the URL, so that xdebug is available straight away.


## Xdebug quickstart experiment

I've currently committed the xdebug settings which are contained in the file .idea/workspace.xml

This means that xdebug should work out of the box - the only thing you might need to do is set a breakpoint.

Alternatively, the instructions for setting up xdebug from scratch below.

## How to setup xdebug with the example project

* Click + to add new "PHP Web page"

* In the name field type "WebDebug" or similar. 

* In the Server field click "..." to create a server

* In the server popup modal enter:

** Name: local.app.basereality.com
** Host: local.app.basereality.com
** Port: 8001
** Check the "Use path mappings" and setup the root of the example project to be "/var/www"

For me the example project is in "/projects/danack/github/example" so that is mapped to "/var/www".

Then click apply and Ok.

* Make sure the "local.app.basereality.com" is selected.

* Enter "/" for the path.

* You should now see "http://local.app.basereality.com:8001/" below the path.

* Click apply/ok.

* Click the 'telephone' button at the top of the screen to start listening for debug sessions. 

You should now have xdebug working. 

## Testing xdebug is working

* Open the file example/lib/index.php and set breakpoint on the line `$app = $injector->make(\Slim\App::class);`. The easiest way I find to do that, is to click in the 'gutter' to the right of the line number, which sets a breakpoint on that line.

* Go to "http://local.app.basereality.com:8001/" in a browser.

Doing that should result in PhpStorm breaking 'aka stopping' on that line, and you can use the debug controls to step through your code.
