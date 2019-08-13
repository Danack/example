<?php

use AurynConfig\InjectionParams;

function injectionParams()
{
    // These classes will only be created once by the injector.
    $shares = [
        \Redis::class,
//        \SlimSession\Helper::class,
        \Twig_Environment::class,
        \Auryn\Injector::class,
//        \Birke\Rememberme\Authenticator::class,
        Doctrine\ORM\EntityManager::class
    ];

    // Alias interfaces (or classes) to the actual types that should be used
    // where they are required.
    $aliases = [
        Example\Repo\BookListRepo\BookListRepo::class => Example\Repo\BookListRepo\DoctrineBookListRepo::class,
        Example\Repo\InvoiceRepo\InvoiceRepo::class => \Example\Repo\InvoiceRepo\FakeInvoiceRepo::class,
        \VarMap\VarMap::class => \Example\Psr7InputMapWithRouteParams::class,
        \BackgroundWorkerExample\ImageJobRepo::class =>
            \BackgroundWorkerExample\RedisImageJobRepo::class,

        \Example\Service\LocalStorage\InvoiceLocalStorage\InvoiceLocalStorage::class =>
        \Example\Service\LocalStorage\InvoiceLocalStorage\FileInvoiceLocalStorage::class,
        \Example\Queue\PrintUrlToPdfQueue::class => \Example\Queue\RedisPrintUrlToPdfQueue::class,

        \Example\Repo\WordRepo\WordRepo::class =>
        \Example\Repo\WordRepo\PdoWordRepo::class,
    ];

    // Delegate the creation of types to callables.
    $delegates = [
        \Psr\Log\LoggerInterface::class => 'createLogger',
        \PDO::class => 'createPDO',
        \Redis::class => '\createRedis',
        \Slim\App::class => 'createAppForSite',
        \Twig_Environment::class => 'createTwigForSite',
        Doctrine\ORM\EntityManager::class => 'createDoctrineEntityManager',
        \Example\Service\LocalStorage\InvoiceLocalStorage\FileInvoiceLocalStorage::class => 'createFileInvoiceLocalStorage',
        \SlimAuryn\Routes::class => 'createRoutesForApp',
        \SlimAuryn\SlimAurynInvokerFactory::class => 'createSlimAurynInvokerFactory',
        \SlimAuryn\ExceptionMiddleware::class => 'createExceptionMiddleware',

        \Slim\Container::class => 'createSlimContainer',
        \Slim\App::class => 'createSlimAppForApp',
    ];

//    if (getConfig(['example', 'direct_sending_no_queue'], false) === true) {
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


