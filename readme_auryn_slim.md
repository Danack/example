
# Slim Auryn Invoker

The 'Slim Auryn Invoker' is a library that enables you to use Auryn within the Slim framework, and uses Auryn to call your controllers, rather than using the standard Slim dispatching.


Additionally the 'Slim Auryn Invoker' has a feature called 'response mapping'. This allows you to return 'stub responses' from your controllers, and to have those stub responses be mapped onto the PSR7 response object rather than handle PSR7 objects directly. This saves a significant amount of code complexity.

Also additionally it allows you to have a routes file to define the routes, rather than inline routing. And finally, it allows you to have setup functions for each routes that are called before the route is dispatched.


## Brief Introduction to Auryn

Auryn is a recursive dependency injection container. 

Instead of having to type out lots of code to create dependencies by hand, you give the Auryn injector a set of instructions of how to create dependencies and then it can execute your code for you.

A very simple example:

```

// We have an interface
interface Writer {
    public function write(string $output);
}

// We have an implementation that implements that interface
class StdOutWriter implements Writer {
    public function write(string $output)
    {
        echo $output;
    }
}

// We have a function that needs any kind of writer 
function foo(Writer $writer)
{
    $writer->write("This was output by foo.");
} 


// We create the injector
$injector = new \Auryn\Injector();


// We tell the injector that whenever something needs a Writer, give them an instance of StdOutWriter
$injector->alias(Writer::class, StdOutWriter::class); 

// Execute the function 'foo'
$injector->execute('foo');

```

More details of what Auryn can do can be found at https://github.com/rdlowrey/Auryn
 
Please note this example only uses 'shares', 'aliases' and 'delegates'. Although the other features are useful when handling legacy code, they aren't absolutely needed for modern code.



## Dependency injection config

Each of the separate environments (i.e. cli, api, admin etc) have their own dependency injection config. This allows for sensitive classes (e.g. system admin tools that can alter user accounts) to only be creatable in the appropriate environment.

As they are a 'top-level' concept they are all held in the directory ./example/injectionParams.

e.g. ./example/injectionParams/api.php, ./example/injectionParams/app.php, ./example/injectionParams/cli.php

Each of those files contains an array each for 'Shares', 'Aliases' and 'Delegates' (as well as arrays for the other unused Auryn config possibilities).   


### Shares

By putting a classname in the list of 'shares' we are telling Auryn that only one instance of an object should be created, and that instance should be injected wherever an instance of an object of that type need to be injected.

### Aliases

The list of aliases contains a list of key value pairs where the key is the name of one type, and the value is the name of a child type of that key.

The list tells Auryn 'when something requires this type actually create this subtype instead'. 

Usually, this will be a big list of interfaces and implementation for each that should be used.

### Delegates

The list of delegates is a list that has a class name for each of the keys, and a callable name that should be be called to create that type of object.

In this example project, the file ./example/lib/factories.php contains all of the delegates functions aka the functions that create objects.

An example delegate function to create a Redis object looks like:

```
function createRedis(Config $config)
{
    $redisConfig = $config->getRedisConfig();
    $redis = new Redis();
    $redis->connect($redisConfig->getHost(), $redisConfig->getPort());
    $redis->auth($redisConfig->getPassword());
    $redis->ping();

    return $redis;
}
```


In the delegates entry in the injection params, this would be defined in the delegates list like: 

```
$delegates = [
    ...
    \Redis::class => 'createRedis',
    ...
];
```

i.e. we tell Auryn that if it ever needs to create a Redis object, it should use the function 'createRedis' to do so.


## Secrets config

There is a config file at ./example/config.php separate from the dependency injection information. The config.php file contains raw secrets e.g. api keys.

For this example project, this secrets config.php file is held in source control. For real applications it should not be in source control (except maybe in an encrypted format). For production systems to that file should be managed by whichever deployment tool you use



## Response mapping

Life is short. 

Life is too short to be dealing with PSR7 Response objects in controller code directly. Compare and contrast some code that returns a json `{"status": "ok"}` response. 

First with PSR 7:

```
function aliveEndPoint(Response $response)
{

    $body = new Stream();
    $stream->write(json_encode(['status' => 'ok']));
    $response = (new Response())
      ->withStatus(200, 'OK!')
      ->withHeader('Content-Type', 'application/json')
      ->withBody($stream);

    return $response
}

```

Then using a stub response:

```
function aliveEndPoint(Response $response)
{
    return new JsonResponse(['status' => 'ok']);
}

```



### Response mapping - how it works



When we create the `SlimAuryn\SlimAurynInvokerFactory`, which is the object that is used to invoke your controllers with Auryn, one of the parameters that is passed in is an array of ResponseMappers. 

This list of types and 'response mappers' allows you to tell the SlimAurynInvoker that 'if a controller returns this type, use this function to map it into the PSR7 response object.

The 'response mappers' setup in the example to map response are:

* A SlimAuryn stub response.

* An actual PSR 7 response object - which just gets passed through.

* A string.

* A twig response object.

This list is defined by this array in the function getResponseMappers.

```
[
    SlimAuryn\Response\StubResponse::class => 'SlimAuryn\ResponseMapper\ResponseMapper::mapStubResponseToPsr7',

    ResponseInterface::class => 'SlimAuryn\ResponseMapper::passThroughResponse',
    
    'string' => 'convertStringToHtmlResponse',
        
    \SlimAuryn\Response\TwigResponse::class => $twigResponseMapperFn
];
```


Just to show there's no deep magic going on, the response mapper for mapping strings into the PSR7 Response object is:

```
// Define a function that writes a string into the response object.
function convertStringToHtmlResponse(string $result, ResponseInterface $response)
{
    $response = $response->withHeader('Content-Type', 'text/html');
    $response->getBody()->write($result);
    return $response;
}
```

i.e. it just does the boring wiring together.


## Routing

The routes for each of the environments (api, app, admin) are defined in the directory ./example/routes with one route file per environment. e.g. api_routes.php.

For each of the environments there is a function to create a SlimAuryn\Routes object, that looks like:

```
function createRoutesForApi()
{
    return new \SlimAuryn\Routes(__DIR__ . '/../routes/api_routes.php');
}
```

And in the injection parameters for each of the environments there is a delegate function that 

```
$delegates = [
    ...
    \SlimAuryn\Routes::class => 'createRoutesForApi',
    ...
];
```


### Routing config

The data for the routing should be a list of routes, with 3 or entries per route:  

* The path to match
* The method to match
* The callable to call when that route is dispatched 
* (optional) A setup callable to call before the route is dispatched.

e.g.

```
['/word_search', 'GET', 'Example\ApiController\Words::searchForWords'],
```