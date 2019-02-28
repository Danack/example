<?php

$options = [];

// Determine if we can use the 'host.docker.internal' name.
$dockerHost  = '10.254.254.254';

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


$options['twig'] = [
    'cache' => false,
    'debug' => true
];
