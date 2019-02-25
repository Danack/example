<?php

namespace Example\Route;

// Each row of this array should return an array of:
// - The path to match
// - The method to match
// - The route info
// - (optional) A setup callable to add middleware/DI info specific to that route
//
// This allows use to configure data per endpoint e.g. the endpoints that should be secured by
// an api key, should call an appropriate callable.
return [
    ['/', 'GET', 'Example\AdminController\Index::get'],

    ['/test/caught_exception', 'GET', 'Example\ApiController\Debug::testCaughtException'],
    ['/test/uncaught_exception', 'GET', 'Example\ApiController\Debug::testUncaughtException'],

    ['/{any:.*}', 'GET', 'Example\SiteController\Index::get'],
];
