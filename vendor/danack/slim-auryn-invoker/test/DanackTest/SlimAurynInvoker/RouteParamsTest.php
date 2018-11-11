<?php

namespace DanackTest\SlimAurynInvoker;

use DanackTest\BaseTestCase;
use Danack\SlimAurynInvoker\RouteParams;
use Danack\SlimAurynInvoker\RouteParamsException;


class RouteParamsTest extends BaseTestCase
{
    public function testSetInjectorInfo()
    {
        $routeParams = new RouteParams([]);
        $this->expectException(RouteParamsException::class);
        $routeParams->getValue('none_existent_key');
    }
}
