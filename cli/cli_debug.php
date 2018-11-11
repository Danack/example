<?php

use Danack\Console\Application;
use Danack\Console\Output\BufferedOutput;
use Example\CLIFunction;



error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../lib/factories.php';
require __DIR__ . '/../lib/exception_mappers.php';
require __DIR__ . "/../lib/cli_functions.php";

CLIFunction::setupErrorHandlers();

if (isset($callable) !== true || isset($params) !== true) {
  echo "Please set \$callable and \$params in the file that includes this one.";
  exit(-1);
}

$actualAliases = [];

if (isset($aliases) === true) {
    $actualAliases = $aliases;
}


if (isset($body) !== true) {
    $body = null;
}



$result = runSomething($callable, $params, $actualAliases, $body);

if ($result instanceof Example\Response\Response) {
    echo $result->getBody();
}
//else {
//    if (is_object($result) === true) {
//        echo "Unknown result type " . get_class($result);
//    }
//    else {
//        echo "Unknown result of " . var_dump($result);
//    }
//}
