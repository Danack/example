<?php

use AurynConfig\InjectionParams;

function injectionParams()
{
    // These classes will only be created once by the injector.
    $shares = [
        \SlimSession\Helper::class,
        \Auryn\Injector::class,
        \Doctrine\ORM\EntityManager::class,
        \Birke\Rememberme\Authenticator::class,
        \Airbrake\Notifier::class
    ];

    // Alias interfaces (or classes) to the actual types that should be used
    // where they are required.
    $aliases = [
        \Example\Route\Routes::class => \Example\Route\ApiRoutes::class,
        \Example\Service\OrderNumberEncoder\OrderNumberEncoder::class => \Example\Service\OrderNumberEncoder\HashidOrderNumberEncoder::class,

        \VarMap\VarMap::class => \VarMap\Psr7InputMapWithVarMap::class,
        \Params\Input::class => \Example\Psr7Input::class,
        \Example\Route\Routes::class => \Example\Route\ApiRoutes::class,
    ];

    // Delegate the creation of types to callables.
    $delegates = [
        \Psr\Log\LoggerInterface::class => 'createLogger',
        \PDO::class => 'createPDO',
        \Doctrine\ORM\EntityManager::class => 'createDoctrineEntityManager',
        \Redis::class => 'createRedis',
//        \Twilio\Rest\Client::class => 'createTwilio',

//        Airbrake\Notifier::class => 'createAirbrakeNotifier',
//        \Example\Service\ExceptionNotifier\ExceptionNotifier::class => 'createExceptionNotifier',

        \Slim\App::class => 'createAppForApi',
    ];

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
