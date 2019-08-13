<?php

declare (strict_types = 1);



/**
 * This file contains factory functions that create objects from either
 * configuration values, user input or other external data.
 */
use Auryn\Injector;
use Example\Config;
use Psr\Http\Message\ResponseInterface;

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
        $pdo = new \PDO($string, $config['username'], $config['password'], array(
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
    $redisConfig = Config::getRedisConfig();
    $redis = new Redis();
    $redis->connect($redisConfig->getHost(), $redisConfig->getPort());
    $redis->auth($redisConfig->getPassword());
    $redis->ping();

    return $redis;
}

function forbidden(\Auryn\Injector $injector)
{
    $injector->make("Please don't use this object directly; create a more specific type to use.");
}


function createTwigForSite(\Auryn\Injector $injector, Config $config)
{
    // The templates are included in order of priority.
    $templatePaths = [
        __DIR__ . '/../app/template'
    ];

    $twigConfig = $config->getTwigConfig();

    $loader = new Twig_Loader_Filesystem($templatePaths);
    $twig = new Twig_Environment($loader, array(
        'cache' => $twigConfig->isCache(),
        'strict_variables' => true,
        'debug' => $twigConfig->isDebug()
    ));

    // Inject function - allows DI in templates.
    $function = new Twig_SimpleFunction('inject', function (string $type) use ($injector) {
        return $injector->make($type);
    });
    $twig->addFunction($function);


    $rawParams = ['is_safe' => array('html')];

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



function createTwigForAdmin(\Auryn\Injector $injector, Config $config)
{
    $twigConfig = $config->getTwigConfig();

    // The templates are included in order of priority.
    $templatePaths = [
        __DIR__ . '/../admin/template'
    ];

    $loader = new Twig_Loader_Filesystem($templatePaths);
    $twig = new Twig_Environment($loader, array(
        'cache' => $twigConfig->isCache(),
        'strict_variables' => true,
        'debug' => $twigConfig->isDebug()
    ));

    // Inject function - allows DI in templates.
    $function = new Twig_SimpleFunction('inject', function (string $type) use ($injector) {
        return $injector->make($type);
    });
    $twig->addFunction($function);


    $rawParams = ['is_safe' => array('html')];
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

/**
 * @return \Doctrine\ORM\EntityManager
 */
function createDoctrineEntityManager()
{
    $config = getConfig(\Example\Config::EXAMPLE_DATABASE_INFO);

    $connectionParams = array(
        'dbname' => $config['schema'],
        'user' => $config['username'],
        'password' => $config['password'],
        'host' => $config['host'],
        'driver' => 'pdo_mysql',
    );

    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
        [__DIR__ . "/Example/Model"],
        true,
        __DIR__ . "/../var/doctrine"
    );

    // TODO - precompile these in the build step.
    // $config->setAutoGenerateProxyClasses(\Doctrine\Common\Proxy\AbstractProxyFactory::AUTOGENERATE_ALWAYS);

    // obtaining the entity manager
    return \Doctrine\ORM\EntityManager::create($connectionParams, $config);
}


function createPdfGeneratorFromConfig() : \Example\CliController\PdfGenerator
{
    return new \Example\CliController\PdfGenerator(
        'http://10.254.254.254:9222',
        __DIR__ . '../var/tmp_pdf'
    );
}

function createFileInvoiceLocalStorage()
{
    $path = __DIR__ . '/../var/files_invoice';

    return new \Example\Service\LocalStorage\InvoiceLocalStorage\FileInvoiceLocalStorage($path);
}

function createRoutesForApp()
{
    return new \SlimAuryn\Routes(__DIR__ . '/../routes/app_routes.php');
}

function createRoutesForAdmin()
{
    return new \SlimAuryn\Routes(__DIR__ . '/../routes/admin_routes.php');
}

function createRoutesForApi()
{
    return new \SlimAuryn\Routes(__DIR__ . '/../routes/api_routes.php');
}

function getExceptionHandlersForApi()
{
    $exceptionHandlers = [
        \Params\Exception\ValidationException::class => 'paramsValidationExceptionMapper',
        \PDOException::class => 'pdoExceptionMapper',
        \Example\Exception\DebuggingCaughtException::class => 'debuggingCaughtExceptionExceptionMapper'
    ];

    return $exceptionHandlers;
}

function createExceptionMiddlewareForApp(\Auryn\Injector $injector)
{
    $exceptionHandlers = [
        \Params\Exception\ValidationException::class => 'paramsValidationExceptionMapper'
    ];

    $resultMappers = getResultMappers($injector);

    return new \SlimAuryn\ExceptionMiddleware(
        $exceptionHandlers,
        $resultMappers
    );
}

function getResultMappers(\Auryn\Injector $injector)
{
    $twigResponseMapperFn = function (
        \SlimAuryn\Response\TwigResponse $twigResponse,
        ResponseInterface $originalResponse
    ) use ($injector) {
        $twigResponseMapper = $injector->make(\SlimAuryn\ResponseMapper\TwigResponseMapper::class);

        return $twigResponseMapper($twigResponse, $originalResponse);
    };

    return [
        \SlimAuryn\Response\StubResponse::class => 'SlimAuryn\ResponseMapper\ResponseMapper::mapStubResponseToPsr7',
        Example\Response\Response::class => 'exampleResponseMapper',
        ResponseInterface::class => 'SlimAuryn\ResponseMapper::passThroughResponse',
        'string' => 'convertStringToHtmlResponse',
        \SlimAuryn\Response\TwigResponse::class => $twigResponseMapperFn
    ];
}

function createExceptionMiddleware(\Auryn\Injector $injector)
{
    return new SlimAuryn\ExceptionMiddleware(
        getExceptionMappers(),
        getResultMappers($injector)
    );
}

function createSlimAurynInvokerFactory(
    \Auryn\Injector $injector,
    \SlimAuryn\RouteMiddlewares $routeMiddlewares
) {
    $resultMappers = getResultMappers($injector);

    return new SlimAuryn\SlimAurynInvokerFactory(
        $injector,
        $routeMiddlewares,
        $resultMappers
    );
}






function createSlimAppForApp(Injector $injector, \Slim\Container $container)
{
    // quality code.
    $container['foundHandler'] = $injector->make(\SlimAuryn\SlimAurynInvokerFactory::class);

//    // TODO - this shouldn't be used in production.
//    $container['errorHandler'] = 'getAppErrorHandler';
//
//    $container['phpErrorHandler'] = function ($container) {
//        return $container['errorHandler'];
//    };

    $app = new \Slim\App($container);

    $app->add($injector->make(\SlimAuryn\ExceptionMiddleware::class));
//    $app->add($injector->make(\Osf\Middleware\ContentSecurityPolicyMiddleware::class));
////    $app->add($injector->make(\Osf\Middleware\BadHeaderMiddleware::class));
//    $app->add($injector->make(\Osf\Middleware\AllowedAccessMiddleware::class));
//    $app->add($injector->make(\Osf\Middleware\MemoryCheckMiddleware::class));

    return $app;
}



function createAppForApi(\Slim\Container $container, \Auryn\Injector $injector)
{
    $app = new \Slim\App($container);

    // TODO - this shouldn't be used in production.
    // TODO - convert to JSON response
    $container['notFoundHandler'] = function ($c) {
        return new \Example\SlimNotFoundHandler($c);
    };

    $app->add($injector->make(\Example\Middleware\AllowAllCors::class));

    return $app;
}

function createAppForSite(\Slim\Container $container, \Auryn\Injector $injector)
{
    $app = new \Slim\App($container);

    // TODO - this shouldn't be used in production.
    $container['errorHandler'] =  'getAppErrorHandler';

    $container['phpErrorHandler'] = function ($container) {
        return $container['errorHandler'];
    };

    return $app;
}

function createAppForAdmin(\Slim\Container $container, \Auryn\Injector $injector)
{
    $app = new \Slim\App($container);

    // TODO - this shouldn't be used in production.
    $container['errorHandler'] = 'getAppErrorHandler';

    $container['phpErrorHandler'] = function ($container) {
        return $container['errorHandler'];
    };

    return $app;
}

function createSlimAppForAdmin(Injector $injector, \Slim\Container $container)
{
    // quality code.
    $container['foundHandler'] = $injector->make(\SlimAuryn\SlimAurynInvokerFactory::class);

    // TODO - this shouldn't be used in production.
    $container['errorHandler'] = 'appErrorHandler';

    $container['phpErrorHandler'] = function ($container) {
        return $container['errorHandler'];
    };

    $app = new \Slim\App($container);
    $app->add($injector->make(\SlimAuryn\ExceptionMiddleware::class));

    return $app;
}



function createSlimContainer()
{
    $container = new \Slim\Container();

    // If there is a global request object, which
    global $request;

    if (isset($request) && $request !== null) {
        $container['request'] = $request;
    }

    return $container;
}
