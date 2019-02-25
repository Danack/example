<?php

use SlimAuryn\SlimAurynInvokerFactory;
use SlimAurynExample\AllRoutesMiddleware;
use SlimAuryn\ExceptionMiddleware;

error_reporting(E_ALL);

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . '/../factories.php';
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

// Create the app with the container set to use SlimAurynInvoker
// for the 'foundHandler'.
$container = new \Slim\Container;
$container['foundHandler'] = $injector->make(SlimAurynInvokerFactory::class);
$app = new \Slim\App($container);

// Configure any middlewares that should be applied to all routes here.
$app->add(new AllRoutesMiddleware());

// Create a middleware that catches all otherwise uncaught application
// level exceptions.
$app->add($injector->make(ExceptionMiddleware::class));


// Setup the routes for the app
setupRoutes($app);

// Run!
$app->run();
