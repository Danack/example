<?php

use AurynConfig\InjectionParams;

if (function_exists('injectionParams') == false) {

    function injectionParams() : InjectionParams
    {
        // These classes will only be created once by the injector.
        $shares = [
            \Doctrine\ORM\EntityManager::class,
            \Airbrake\Notifier::class
        ];

        // Alias interfaces (or classes) to the actual types that should be used
        // where they are required.
        $aliases = [
            Example\Repo\InvoiceRepo\InvoiceRepo::class => \Example\Repo\InvoiceRepo\FakeInvoiceRepo::class,

            \Example\Service\LocalStorage\InvoiceLocalStorage\InvoiceLocalStorage::class =>
            \Example\Service\LocalStorage\InvoiceLocalStorage\FileInvoiceLocalStorage::class,
            \Example\Queue\PrintUrlToPdfQueue::class => \Example\Queue\RedisPrintUrlToPdfQueue::class,
        ];



        // Delegate the creation of types to callables.
        $delegates = [
            \PDO::class => 'createPDO',
            \Redis::class => 'createRedis',
            \Doctrine\ORM\EntityManager::class => 'createDoctrineEntityManager',
            \Example\Service\LocalStorage\InvoiceLocalStorage\FileInvoiceLocalStorage::class => 'createFileInvoiceLocalStorage',
            \Example\CliController\PdfGenerator::class => 'createPdfGeneratorFromConfig',

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
}


return injectionParams();
