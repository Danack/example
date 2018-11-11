<?php

use Danack\SlimAurynInvoker\SlimAurynInvokerFactory;

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

// Create the app with the container set to use SlimAurynInvoker
// for the 'foundHandler'.
$container = new \Slim\Container;
$container['foundHandler'] = new SlimAurynInvokerFactory($injector);
$app = new \Slim\App($container);

// Configure any middlewares here.

// Setup the routes for the app
setupBasicRoutes($app);

// Run!
$app->run();