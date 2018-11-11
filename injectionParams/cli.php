<?php

use AurynConfig\InjectionParams;

if (function_exists('injectionParams') == false) {

    function injectionParams() : InjectionParams
    {
        // These classes will only be created once by the injector.
        $shares = [
            \SlimSession\Helper::class,
            \Doctrine\ORM\EntityManager::class,
            \Airbrake\Notifier::class
        ];

        // Alias interfaces (or classes) to the actual types that should be used
        // where they are required.
        $aliases = [
            \Example\Service\OrderNumberEncoder\OrderNumberEncoder::class => \Example\Service\OrderNumberEncoder\HashidOrderNumberEncoder::class,
            \Example\Repo\ProductRepo\ProductRepo::class => \Example\Repo\ProductRepo\DoctrineProductRepo::class,

            \Example\Repo\CustomerOrderRepo\CustomerOrderRepo::class =>
                \Example\Repo\CustomerOrderRepo\DoctrineCustomerOrderRepo::class,

            \Example\Repo\OrderRepo\OrderRepo::class =>
            \Example\Repo\OrderRepo\DoctrineOrderRepo::class,
            \Example\Service\EmailNotifier\EmailNotifier::class =>
            \Example\Service\EmailNotifier\NullEmailNotifier::class,

        ];

        // Delegate the creation of types to callables.
        $delegates = [
            \PDO::class => 'createPDO',
            \Redis::class => 'createRedis',
            \Doctrine\ORM\EntityManager::class => 'createDoctrineEntityManager',
            \Twilio\Rest\Client::class => 'createTwilio',
            \Example\Service\SmsNotifier\SmsNotifier::class => 'createSmsNotifier',
            \Mandrill::class => 'createMandrill',
            \Example\MandrillConfig::class => 'createMandrillConfig',
            Example\TwilioConfig::class => 'createTwilioConfig',
            Airbrake\Notifier::class => 'createAirbrakeNotifier',
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
