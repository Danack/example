<?php

declare(strict_types=1);


function runSomething(callable $callable, $params, $aliases, $body)
{
    $injector = new Auryn\Injector();

    /** @var $cliInjectionParams \AurynConfig\InjectionParams*/
    $cliInjectionParams = require __DIR__ . "/../injectionParams/cli.php";

    $cliInjectionParams->mergeSharedObjects($aliases);


    $cliInjectionParams->addToInjector($injector);

    $injector->share($injector);

    $exceptionMappers = [
        Auryn\InjectionException::class => 'cliHandleInjectionException',
    ];

    try {
        foreach ($params as $key => $value) {
            $injector->defineParam($key, $value);
        }

        $variableMap = new \VarMap\ArrayVarMap($params);
        $injector->alias(\VarMap\VarMap::class, get_class($variableMap));
        $injector->share($variableMap);


        if ($body !== null) {
            $data = json_decode_safe($body);

            $valueInput = new \Params\ValueInput($data);
            $injector->alias(\Params\Input::class, get_class($valueInput));
            $injector->share($valueInput);
        }

        return $injector->execute($callable);
    } catch (\Exception $e) {
        echo $e->getMessage();
        echo $e->getTraceAsString();
        // TODO - improve this stuff.
//        foreach ($exceptionMappers as $exceptionType => $handler) {
//            if ($e instanceof $exceptionType) {
//                $handler($console, $e);
//                return;
//            }
//        }
//
//        cliHandleGenericException($console, $e);
    }

    return null;
}
