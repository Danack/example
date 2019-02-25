<?php

namespace SlimAurynTest;

use SlimAurynTest\BaseTestCase;
use SlimAuryn\RouteParams;
use SlimAuryn\RouteParamsException;


class RouteParamsTest extends BaseTestCase
{
    public function testSetInjectorInfo()
    {
        $routeParams = new RouteParams([]);
        $this->expectException(RouteParamsException::class);
        $routeParams->getValue('none_existent_key');
    }
}
