
# Slim Auryn Invoker

[![Build Status](https://travis-ci.org/Danack/SlimAurynInvoker.svg?branch=master)](https://travis-ci.org/Danack/SlimAurynInvoker)

This library allows you to use the [Auryn Dependency Injection](https://github.com/rdlowrey/auryn) library as the dispatching layer of a [Slim Framework](https://www.slimframework.com/) application. 

It replaces the built-in dispatcher that executes the callables for routes, and instead executes the callables through Auryn.

It does not affect the dispatching of middlewares.

## Quickstart

If you install this library though Composer, all you need to do to link it into a Slim application is to set the 'foundHandler' when creating the Slim app.


```
<?php

use Danack\SlimAurynInvoker\SlimAurynInvokerFactory;

$container = new \Slim\Container;
$container['foundHandler'] = new SlimAurynInvokerFactory($injector);
$app = new \Slim\App($container);
```

There is an example app in the example directory

## Why you should use this library

There are two main reasons to use Auryn as the tool that executes your controllers in your application; it allows you to use interface segregation for passing in parameters, as well allowing your controllers to return simple 'stub response' types, rather than having to touch the PSR 7 response object.


### Inteface segregation for parameters

@todo - explain why interface segration is so awesome for controllers.

I have a talk that I have given at a couple of user groups and PHPNW - [https://www.youtube.com/watch?v=YKXfOYTBaI4](https://www.youtube.com/watch?v=YKXfOYTBaI4) for which the slides are available [here](http://docs.basereality.com/InterfaceSegregationPHPNW/#/). 


### Simple typed return values

I pretty strongly believe that most of the time, it should be left to the framework to connect up a 'stub response' from a controller to the underlying library that will be used to actually send it to a user.

The SlimAurynInvoker allows you to return a 'stub response' object that consists of just the status code, body and a set of headers that the controller wishes to send as the response. The library 

This makes life much more pleasant. Compare two equivalent controllers. The first controller directly modifies the Response object.

```
function psr7JsonController(Request $request, Response $response)
{
    $data = ['foo' => 'bar'];
    $json = json_encode($data);

    $response->getBody()
      ->write($json)
      ->withHeader('Content-type', 'application/json');
      return $response;
}
```

The second controller simply returns a stub JsonResponse.

```
function simpleJsonController(Request $request, Response $response)
{
    $data = ['foo' => 'bar'];

    return new JsonResponse($data);
}
```

By returning a stub response object, it not only remove a lot of tedious, fiddly code from your controllers, but also makes it easier to:

* Understand what type of data the controller is returning.
* Manage the details of which headers are sent for which response type in one place, rather than having to alter every controller that returns that type.
* Test your controllers are returning the correct data type.


## Setup and result processing

The steps before and after the controller is dispatched can be controlled by the user. By default the steps are defined as follows: 

### Setting up before the controller is dispatched

By default the SlimAurynInvoker does these actions.

* Shares the request object through the injector, so that controllers can have it as a dependency.
* Shares the response object through the injector, so that controllers can have it as a dependency.
* Defines the route parameters as named parameters so that controllers can have the route arguments as dependencies by name.
* Creates and shares a \Danack\SlimAurynInvoker\RouteParams object to that controllers can have the route pamaters as a dependency by type.


### Result processing

By default after dispatching the controller, the SlimAurynInvoker will: 

* If an object of type \Danack\Response\StubResponse was returned by the controller, the SlimAurynInvoker will [map it into the Psr 7 response object](https://github.com/Danack/SlimAurynInvoker/blob/71b0b5760e05e0abbab77aad2f94a6a58625afd9/lib/Danack/Response/StubResponseMapper.php#L19-L29).

* If an object of type Psr 7 Response is returned, SlimAurynInvoker will just pass it back to Slim.


### Customising the setup and result processing

If you want to customise either the setup or result processing functionality, you can do that by passing appropriate callables as the $resultMappers and/or $setupFunction into the \Danack\SlimAurynInvoker\SlimAurynInvokerFactory.


The resultMapper array should be an array, where the keys are the types that are returned by the controller, and the values are callables, with the signature:

```
function ($builtResponse, ResponseInterface $response);
```

A setup callable should have this signature:

```
function(
        Injector $injector,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    );
```

An example that show an alternative resultMapper is in the file ./example/public/index_html.php. It shows how you can configure the SlimAurynInvoker, so that each controller can just return a string, which automatically gets sent as a HTML response.

## Running the example

There is a simple example app in the example folder, which can be run by using PHP's built-in webserver. You can run it by running this:

```
php -S 0.0.0.0:8000 -t example/public
```

in the root directory of this library, and then going to http://127.0.0.1:8000/ in your browser.

Additionally, there is an example of a controller returning just a string, which is mapped into a HTML response at http://127.0.0.1:8000/index_html.php


## Notes

### How to run the unit tests

php vendor/bin/phpunit -c test/phpunit.xml

### How to run the code style check

php vendor/bin/phpcs --standard=./test/codesniffer.xml --encoding=utf-8 --extensions=php -p -s lib



