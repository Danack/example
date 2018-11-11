<?php

declare (strict_types = 1);


/**
 * This file contains factory functions that create objects from either
 * configuration values, user input or other external data.
 *
 */

use Example\Config;
use Danack\Response\StubResponse;
use Psr\Http\Message\ResponseInterface;
use Danack\SlimAurynInvoker\SlimAurynInvokerFactory;
use Danack\Response\StubResponseMapper;

/**
 * @return \Monolog\Logger
 * @throws Exception
 */
function createLogger()
{
    $log = new \Monolog\Logger('logger');
    $directory = __DIR__ . "/../var";
    if (!@mkdir($directory) && !is_dir($directory)) {
        throw new \Exception("Log directory doesn't exist.");
    }

    $filename = $directory . '/oauth.log';

    $log->pushHandler(new \Monolog\Handler\StreamHandler($filename, \Monolog\Logger::WARNING));

    return $log;
}



/**
 * @return PDO
 * @throws Exception
 */
function createPDO()
{
    $config = getConfig(Config::EXAMPLE_DATABASE_INFO);

    $string = sprintf(
        'mysql:host=%s;dbname=%s',
        $config['host'],
        $config['schema']
    );

    try {
        $pdo = new \Pdo($string, $config['username'], $config['password'], array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_TIMEOUT => 3,
            \PDO::MYSQL_ATTR_FOUND_ROWS => true
        ));
    }
    catch (\Exception $e) {
        throw new \Exception(
            "Error creating PDO:" . $e->getMessage(),
            $e->getCode(),
            $e
        );
    }

    return $pdo;
}

/**
 * @return Redis
 * @throws Exception
 */
function createRedis()
{
    $redisInfo = getConfig(Config::EXAMPLE_REDIS_INFO);

    $redis = new Redis();
    $redis->connect($redisInfo['host'], $redisInfo['port']);
    $redis->auth($redisInfo['password']);
    $redis->ping();

    return $redis;
}



function forbidden(\Auryn\Injector $injector)
{
    $injector->make("Please don't use this object directly; create a more specific type to use.");
}


/**
 * @param \Slim\Container $container
 * @param \Auryn\Injector $injector
 * @return \Slim\App
 * @throws \Auryn\InjectionException
 * @throws \Interop\Container\Exception\ContainerException
 */
function createAppForApi(\Slim\Container $container, \Auryn\Injector $injector)
{
    $app = new \Slim\App($container);

    $settings = $container->get('settings');
    $settings->replace([
        'determineRouteBeforeAppMiddleware' => true,
    ]);

    $resultMappers = [
        StubResponse::class => [StubResponseMapper::class, 'mapToPsr7Response'],
        Example\Response\Response::class => 'exampleResponseMapper',
        ResponseInterface::class => function (
            ResponseInterface $controllerResult,
            ResponseInterface $originalResponse
        ) {
            return $controllerResult;
        }
    ];

    $exceptionHandlers = [
        \Params\Exception\ValidationException::class => 'paramsValidationExceptionMapper',
        \PDOException::class => 'pdoExceptionMapper',
        \Example\Exception\DebuggingCaughtException::class => 'debuggingCaughtExceptionExceptionMapper'
    ];

//    $airbrakeWrapper = $injector->make(\Example\Service\ExceptionNotifierWrapper::class);

//    $wrappedExceptionHandlers = [];
//    foreach ($exceptionHandlers as $exceptionType => $exceptionHandler) {
//        $wrappedExceptionHandlers[$exceptionType] = $airbrakeWrapper->wrapExceptionMapper($exceptionHandler);
//    }

    $container['foundHandler'] = new SlimAurynInvokerFactory(
        $injector,
        $resultMappers,
        'setupSlimAurynInvoker',
        $exceptionHandlers
    );

    // TODO - this shouldn't be used in production.
    // TODO - convert to JSON response
    $container['notFoundHandler'] = function ($c) {
        return new \Example\SlimNotFoundHandler($c);
    };

    $app->add($injector->make(\Example\Middleware\AllowAllCors::class));

    // TODO - this shouldn't be used in production.
    // TODO - convert to JSON response

    // TODO - re-enable this
//    $exceptionNotifier = $injector->make(\Example\Service\ExceptionNotifier\ExceptionNotifier::class);
//
//    $container['errorHandler'] = function ($c) use ($exceptionNotifier) {
//        return new \Example\SlimErrorHandler($c, $exceptionNotifier);
//    };

    $routes = $injector->make(\Example\Route\Routes::class);
    $container['router'] = new \Example\ExampleRouter($routes, $injector, $container);

    return $app;
}



function createAppForSite(\Slim\Container $container, \Auryn\Injector $injector)
{
    $resultMappers = [
        StubResponse::class => [StubResponseMapper::class, 'mapToPsr7Response'],
        Example\Response\Response::class => 'exampleResponseMapper',
        ResponseInterface::class => function (
            ResponseInterface $controllerResult,
            ResponseInterface $originalResponse
        ) {
            return $controllerResult;
        }
    ];

    $exceptionHandlers = [
        \Params\Exception\ValidationException::class => 'paramsValidationExceptionMapper'
    ];

    $container['foundHandler'] = new SlimAurynInvokerFactory(
        $injector,
        $resultMappers,
        'setupSlimAurynInvoker',
        $exceptionHandlers
    );

    $app = new \Slim\App($container);

    // TODO - this shouldn't be used in production.
    $container['errorHandler'] = function ($c) {
        return function ($request, $response, $exception) use ($c) {
            /** @var $exception \Throwable */
            $text = "";
            do {
                $text .= $exception->getMessage() . "<br/><br/>\n\n";
                $text .= str_replace("#", "<br/>#", nl2br($exception->getTraceAsString())). "<br/><br/>\n\n";
            } while (($exception = $exception->getPrevious()) !== null);


            error_log($text);
            return $c['response']->withStatus(500)
                ->withHeader('Content-Type', 'text/html')
                ->write($text);
        };
    };

    $container['phpErrorHandler'] = function ($container) {
        return $container['errorHandler'];
    };


    $routes = $injector->make(\Example\Route\Routes::class);
    $container['router'] = new \Example\ExampleRouter($routes, $injector, $container);

    return $app;
}


function createAppForAdmin(\Slim\Container $container, \Auryn\Injector $injector)
{
    $resultMappers = [
        StubResponse::class => [StubResponseMapper::class, 'mapToPsr7Response'],
        Example\Response\Response::class => 'exampleResponseMapper',
        ResponseInterface::class => function (
            ResponseInterface $controllerResult,
            ResponseInterface $originalResponse
        ) {
            return $controllerResult;
        }
    ];

    $exceptionHandlers = [
        \Params\Exception\ValidationException::class => 'paramsValidationExceptionMapper'
    ];

    $container['foundHandler'] = new SlimAurynInvokerFactory(
        $injector,
        $resultMappers,
        'setupSlimAurynInvoker',
        $exceptionHandlers
    );

    $app = new \Slim\App($container);

    // TODO - this shouldn't be used in production.
    $container['errorHandler'] = function ($c) {
        return function ($request, $response, $exception) use ($c) {
            /** @var $exception \Throwable */
            $text = "";
            do {
                $text .= $exception->getMessage() . "<br/><br/>\n\n";
                $text .= str_replace("#", "<br/>#", nl2br($exception->getTraceAsString())). "<br/><br/>\n\n";
            } while (($exception = $exception->getPrevious()) !== null);


            error_log($text);
            return $c['response']->withStatus(500)
                ->withHeader('Content-Type', 'text/html')
                ->write($text);
        };
    };

    $container['phpErrorHandler'] = function ($container) {
        return $container['errorHandler'];
    };


    $routes = $injector->make(\Example\Route\Routes::class);
    $container['router'] = new \Example\ExampleRouter($routes, $injector, $container);

    return $app;
}



function createTwigForSite(\Auryn\Injector $injector)
{
    // The templates are included in order of priority.
    $templatePaths = [
        __DIR__ . '/../app/template'
    ];


    $loader = new Twig_Loader_Filesystem($templatePaths);
    $twig = new Twig_Environment($loader, array(
        'cache' => false,
        'strict_variables' => true,
        'debug' => true // TODO - needs config
    ));

    // Inject function - allows DI in templates.
    $function = new Twig_SimpleFunction('inject', function (string $type) use ($injector) {
        return $injector->make($type);
    });
    $twig->addFunction($function);


    $rawParams = ['is_safe' => array('html')];

    $twigFunctions = [
//        'memory_debug'
    ];

    foreach ($twigFunctions as $functionName => $callable) {
        $function = new Twig_SimpleFunction($functionName, function () use ($injector, $callable) {
            return $injector->execute($callable);
        });
        $twig->addFunction($function);
    }

    $rawTwigFunctions = [
        'memory_debug' => 'memory_debug',
    ];

    foreach ($rawTwigFunctions as $functionName => $callable) {
        $function = new Twig_SimpleFunction($functionName, function () use ($injector, $callable) {
            return $injector->execute($callable);
        }, $rawParams);
        $twig->addFunction($function);
    }

    return $twig;
}



function createTwigForAdmin(\Auryn\Injector $injector)
{
    // The templates are included in order of priority.
    $templatePaths = [
        __DIR__ . '/../admin/template'
    ];


    $loader = new Twig_Loader_Filesystem($templatePaths);
    $twig = new Twig_Environment($loader, array(
        'cache' => false,
        'strict_variables' => true,
        'debug' => true // TODO - needs config
    ));

    // Inject function - allows DI in templates.
    $function = new Twig_SimpleFunction('inject', function (string $type) use ($injector) {
        return $injector->make($type);
    });
    $twig->addFunction($function);


    $rawParams = ['is_safe' => array('html')];

    $twigFunctions = [
//        'memory_debug'
    ];

    foreach ($twigFunctions as $functionName => $callable) {
        $function = new Twig_SimpleFunction($functionName, function () use ($injector, $callable) {
            return $injector->execute($callable);
        });
        $twig->addFunction($function);
    }

    $rawTwigFunctions = [
        'memory_debug' => 'memory_debug',
    ];

    foreach ($rawTwigFunctions as $functionName => $callable) {
        $function = new Twig_SimpleFunction($functionName, function () use ($injector, $callable) {
            return $injector->execute($callable);
        }, $rawParams);
        $twig->addFunction($function);
    }

    return $twig;
}