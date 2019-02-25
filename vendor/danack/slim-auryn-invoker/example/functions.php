<?php

use Psr\Http\Message\ResponseInterface;
use SlimAuryn\Response\StubResponse;
use Slim\App;
use Auryn\Injector;

use SlimAurynTest\Foo\Foo;
use SlimAurynTest\Foo\StandardFoo;
use SlimAuryn\RouteMiddlewares;
use SlimAurynExample\SingleRouteMiddleware;
use SlimAurynExample\SingleRouteWithMessageMiddleware;

/**
 * @return \Monolog\Logger
 */
function createLogger()
{
    $log = new \Monolog\Logger('logger');
    $directory = __DIR__ . "/./var";
    $filename = $directory . '/php_error.log';
    $log->pushHandler(new \Monolog\Handler\StreamHandler($filename, \Monolog\Logger::WARNING));

    return $log;
}

/**
 * @return Twig_Environment
 */
function createTwigForSite()
{
    $templatePaths = [
        __DIR__ . '/./templates' // shared templates.
    ];

    $loader = new Twig_Loader_Filesystem($templatePaths);
    $twig = new Twig_Environment($loader, array(
        'cache' => false,
        'strict_variables' => true,
        'debug' => true
    ));

    return $twig;
}

/**
 * Converting any unexpected code warning/error into an exception is the only
 * sane way to handle unexpected code warnings/errors.
 *
 * @param mixed $errorNumber
 * @param mixed $errorMessage
 * @param mixed $errorFile
 * @param mixed $errorLine
 * @return bool
 * @throws Exception
 */
function saneErrorHandler($errorNumber, $errorMessage, $errorFile, $errorLine)
{
    if (error_reporting() === 0) {
        // Error reporting has been silenced
        return true;
    }
    if ($errorNumber === E_DEPRECATED) {
        return false;
    }
    if ($errorNumber === E_CORE_ERROR || $errorNumber === E_ERROR) {
        // For these two types, PHP is shutting down anyway. Return false
        // to allow shutdown to continue
        return false;
    }
    $message = "Error: [$errorNumber] $errorMessage in file $errorFile on line $errorLine.";
    throw new \Exception($message);
}


function fetchUri($uri, $method, $queryParams = [], $body = null)
{
    $query = http_build_query($queryParams);
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $uri . $query);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

    if ($body !== null) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
    }

    $headers = [];
    $handleHeaderLine = function ($curl, $headerLine) use (&$headers) {
        $headers[] = trim($headerLine);
        return strlen($headerLine);
    };
    curl_setopt($curl, CURLOPT_HEADERFUNCTION, $handleHeaderLine);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $body = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    return [$statusCode, $body, $headers];
}

function getExceptionMappers()
{
    $exceptionMappers = [
    ];

    return $exceptionMappers;
}


// Define a function that writes a string into the response object.
function convertStringToHtmlResponse(string $result, ResponseInterface $response)
{
    $response = $response->withHeader('Content-Type', 'text/html');
    $response->getBody()->write($result);
    return $response;
}

function psr7ResponsePassThrough(
    ResponseInterface $controllerResult,
    ResponseInterface $originalResponse
) {
    return $controllerResult;
}


function mapToPsr7Response(StubResponse $builtResponse, ResponseInterface $response)
{
    $response = $response->withStatus($builtResponse->getStatus());
    foreach ($builtResponse->getHeaders() as $key => $value) {
        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $response->withHeader($key, $value);
    }
    $response->getBody()->write($builtResponse->getBody());

    return $response;
}

function getResultMappers()
{
    return [
        StubResponse::class => 'mapToPsr7Response',
        ResponseInterface::class => 'psr7ResponsePassThrough',
        'string' => 'convertStringToHtmlResponse'
    ];
}


function setupFooAlias(Injector $injector)
{
    $injector->alias(Foo::class, StandardFoo::class);
}



function setupFooDelegate(Injector $injector)
{
    $injector->delegate(Foo::class, 'createFoo');
}


function setupRouteMiddleware(RouteMiddlewares $routeMiddlewares)
{
    $routeMiddlewares->addMiddleware(new SingleRouteMiddleware());
}

function setupRouteMiddlewareForOrderTest(RouteMiddlewares $routeMiddlewares)
{
    $routeMiddlewares->addMiddleware(new SingleRouteWithMessageMiddleware('1st'));
    $routeMiddlewares->addMiddleware(new SingleRouteWithMessageMiddleware('2nd'));
    $routeMiddlewares->addMiddleware(new SingleRouteWithMessageMiddleware('3rd'));
}

// Setup all the routes from the route file.
// Format for each route is:
// [$pattern, $method, $callable, $setupCallable]
// where the setupCallable is optional.
function setupRoutes(App $app)
{
    $routes = require __DIR__ . '/routes.php';

    foreach ($routes as $route) {
        list($path, $method, $callable) = $route;
        $slimRoute = $app->map([$method], $path, $callable);

        if (array_key_exists(3, $route) === true) {
            $setupCallable = $route[3];
            $slimRoute->setArgument('setupCallable', $setupCallable);
        }
    }
}
