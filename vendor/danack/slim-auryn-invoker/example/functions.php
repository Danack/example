<?php

/**
 * @return \Monolog\Logger
 */
function createLogger()
{
    $log = new \Monolog\Logger('logger');
    $directory = __DIR__ . "/./var";
    $filename = $directory . '/php_error.log';
    $log->pushHandler(new \Monolog\Handler\StreamHandler($filename, \Monolog\Logger::WARNING));

    return $log;
}

/**
 * @return Twig_Environment
 */
function createTwigForSite()
{
    $templatePaths = [
        __DIR__ . '/./templates' // shared templates.
    ];

    $loader = new Twig_Loader_Filesystem($templatePaths);
    $twig = new Twig_Environment($loader, array(
        'cache' => false,
        'strict_variables' => true,
        'debug' => true
    ));

    return $twig;
}

/**
 * Converting any unexpected code warning/error into an exception is the only
 * sane way to handle unexpected code warnings/errors.
 *
 * @param $errorNumber
 * @param $errorMessage
 * @param $errorFile
 * @param $errorLine
 * @return bool
 * @throws Exception
 */
function saneErrorHandler($errorNumber, $errorMessage, $errorFile, $errorLine)
{
    if (error_reporting() === 0) {
        // Error reporting has been silenced
        return true;
    }
    if ($errorNumber === E_DEPRECATED) {
        return false;
    }
    if ($errorNumber === E_CORE_ERROR || $errorNumber === E_ERROR) {
        // For these two types, PHP is shutting down anyway. Return false
        // to allow shutdown to continue
        return false;
    }
    $message = "Error: [$errorNumber] $errorMessage in file $errorFile on line $errorLine.";
    throw new \Exception($message);
}
