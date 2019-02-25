<?php

use Auryn\Injector;

require_once(__DIR__.'/../vendor/autoload.php');
require_once __DIR__ . '/../injectionParams/cliTest.php';
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../lib/factories.php';

/**
 * @param array $testAliases
 * @return \Auryn\Injector
 */
function createInjector($testDoubles = [], $testAliases = [])
{
    $injectionParams = injectionParams(
        $testDoubles,
        $testAliases
    );

    $injector = new \Auryn\Injector();
    $injectionParams->addToInjector($injector);

//    foreach ($shareDoubles as $shareDouble) {
//        $injector->share($shareDouble);
//    }

    $injector->share($injector); //Yolo ServiceLocator
    return $injector;
}



