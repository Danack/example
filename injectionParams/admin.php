<?php

use AurynConfig\InjectionParams;

function injectionParams()
{
    // These classes will only be created once by the injector.
    $shares = [
        \Redis::class,
        \SlimSession\Helper::class,
        \Twig_Environment::class,
        \Auryn\Injector::class,
        \Birke\Rememberme\Authenticator::class,
    ];


    // Alias interfaces (or classes) to the actual types that should be used
    // where they are required.
    $aliases = [
        \Example\Route\Routes::class => \Example\Route\AdminRoutes::class,

    ];

    // Delegate the creation of types to callables.
    $delegates = [
        \Psr\Log\LoggerInterface::class => 'createLogger',
        \Twig_Environment::class => 'createTwigForAdmin',
        \PDO::class => 'createPDO',
        \Slim\App::class => 'createAppForAdmin',
    ];

//
//    if (getConfig(['example', 'direct_sending_no_queue']) === true) {
//
//    }


    // Define some params that can be injected purely by name.
    $params = [];

    $prepares = [
    ];

    $defines = [];

    $injectionParams = new InjectionParams(
        $shares,
        $aliases,
        $delegates,
        $params,
        $prepares,
        $defines
    );

    return $injectionParams;
}
