<?php

error_reporting(E_ALL);

$isStaffApiEndpoint = false;

require_once __DIR__ . "/../vendor/autoload.php";

require_once __DIR__ . '/factories.php';
require_once __DIR__ . '/exception_mappers_http.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/slim_functions.php';
require_once __DIR__ . '/twig_functions.php';

use SlimAuryn\ExceptionMiddleware;
use SlimAuryn\SlimAurynInvokerFactory;
use SlimAuryn\Routes;

set_error_handler('saneErrorHandler');

$injector = new Auryn\Injector();
$injectionParams = injectionParams();
$injectionParams->addToInjector($injector);
$injector->share($injector);

try {
    $container = new \Slim\Container;
    $injector->share($container);

    $container['foundHandler'] = $injector->make(SlimAurynInvokerFactory::class);

    $app = $injector->make(\Slim\App::class);
    $routes = $injector->make(Routes::class);
    $routes->setupRoutes($app);

    $app->run();
}
catch (\Exception $exception) {
    echo "Exception in code and Slim error handler failed also: <br/>";
    var_dump(get_class($exception));
    showException($exception);
}
