<?php

$options = [];


// Determine if we can use the 'host.docker.internal' name.
$dockerHost  = '10.254.254.254';
$output = null;
//$output = shell_exec('docker -v');
//preg_match('#Docker version (?P<version>[\d\.]+), build ([\da-z]+)#', $output, $matches);
//if (is_array($matches) && array_key_exists('version', $matches) === true) {
//    $dockerVersionInstalled = $matches['version'];
//    $dockerVersionWithInternalHost = '18.03.0';
//    if (version_compare($dockerVersionInstalled, $dockerVersionWithInternalHost) >= 0) {
//        $dockerHost = 'host.docker.internal';
//    };
//}


$options['example']['database'] = [
    'schema' => 'example',
    'host' => $dockerHost,
    'username' => 'example',
    'password' => 'D9cACV8Pue3CvM93',
];

$options['example']['redis'] = [
    'host' => $dockerHost,
    'password' => 'WfunSEPArNXnB4sh',
    'port' => 6379
];


//'chrome_uri'     => 'http://10.254.254.254:9222',

// production - in production
// production in staging
// 'develop' in develop
// local in local

$options['example']['env'] = 'local';


