<?php

$options = [];

$options['example']['database'] = [
    'schema' => 'example',
    'host' => '10.254.254.254',
    'username' => 'example',
    'password' => 'D9cACV8Pue3CvM93',
];


$options['example']['redis'] = [
    'host' => '10.254.254.254',
    'password' => 'WfunSEPArNXnB4sh',
    'port' => 6379
];


// production - in production
// production in staging
// 'develop' in develop
// local in local

$options['example']['env'] = 'local';


