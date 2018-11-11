<?php

use Danack\SlimAurynInvoker\SlimAurynInvokerFactory;
use Psr\Http\Message\ResponseInterface;

error_reporting(E_ALL);

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../injectionParams.php';
require_once __DIR__ . '/../routes.php';

set_error_handler('saneErrorHandler');

// Setup the Injector
$injector = new Auryn\Injector();
$injectionParams = injectionParams();
$injectionParams->addToInjector($injector);
$injector->share($injector);

// Add any custom rules you'd like to the injector here, or in
// the injectionParams.php file.

// Create a container, so that we can setup
$container = new \Slim\Container;

// Define a function that writes a string into the response object.
$convertStringToHtmlResponse = function(string $result, ResponseInterface $response) {
    $response = $response->withHeader('Content-Type', 'text/html');
    $response->getBody()->write($result);
    return $response;
};

// Create a single result mapper, to convert strings returned from a controller
// into a Psr 7 response with the content-type set.
$resultMappers = [
    'string' => $convertStringToHtmlResponse
];

$container['foundHandler'] = new SlimAurynInvokerFactory($injector, $resultMappers);

// Create the app with the container set to use SlimAurynInvoker
// for the 'foundHandler'.
$app = new \Slim\App($container);

// Configure any middlewares here.

// Setup the routes for the app
setupHtmlRoutes($app);

// Run!
$app->run();