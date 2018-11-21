<?php

$options = [];


// $dockerHost  = '10.254.254.254';
$dockerHost = 'host.docker.internal';

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


// production - in production
// production in staging
// 'develop' in develop
// local in local

$options['example']['env'] = 'local';


