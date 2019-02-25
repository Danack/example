<?php

return [
    ['/test_middleware/middleware_is_added',   'GET',  'SlimAurynExample\TestController::testMiddleware', 'setupRouteMiddleware'],
    ['/test_middleware/middleware_not_added',   'GET',  'SlimAurynExample\TestController::testMiddleware'],
    ['/test_middleware/middleware_correct_order',   'GET',  'SlimAurynExample\TestController::testMiddleware', 'setupRouteMiddlewareForOrderTest'],

    ['/test_di/interface_is_aliased',   'GET',  'SlimAurynExample\TestController::testHowFooIsMade', 'setupFooAlias' ],
    ['/test_di/interface_is_unaliased', 'GET',  'SlimAurynExample\TestController::testHowFooIsMade' ],
    ['/test_di/interface_is_delegated', 'GET',  'SlimAurynExample\TestController::testHowFooIsMade', 'setupFooDelegate' ],

    ['/', 'GET', 'SlimAurynExample\ResponseController::getHomePage'],
];
