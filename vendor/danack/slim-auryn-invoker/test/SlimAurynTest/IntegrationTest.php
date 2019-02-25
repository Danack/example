<?php

declare(strict_types=1);

namespace SlimAurynTest;

use SlimAurynExample\SingleRouteWithMessageMiddleware;

class IntegrationTest extends BaseTestCase
{
    const INTERFACE_FOO_WAS_ALIASED_TO_STANDARD_FOO = 'Interface Foo was aliased to "StandardFoo".';

    const INTERFACE_FOO_CANNOT_BE_MADE_NO_INSTRUCTIONS_FOR_IT = 'Interface Foo cannot be made, no instructions for it.';

    const INTERFACE_FOO_CREATED_THROUGH_DELEGATION = 'Interface Foo created through delegation';

    public function testMiddleWareIsDispatchedCorrectly()
    {
        [$statusCode, $body, $headers] = fetchUri(
            'http://local.slimauryn.basereality.com/test_middleware/middleware_is_added',
            'GET'
        );

        $this->assertEquals(200, $statusCode);
        $this->assertContains('X-all_routes_middleware: active', $headers);
        $this->assertContains('X-single_route_middleware: active', $headers);
    }


    public function testMiddleWareIsDispatchedForRouteNotSetFor()
    {
        [$statusCode, $body, $headers] = fetchUri(
            'http://local.slimauryn.basereality.com/test_middleware/middleware_not_added',
            'GET'
        );

        $this->assertContains("X-all_routes_middleware: active", $headers);
        $this->assertNotContains('X-single_route_middleware: active', $headers);
    }

    public function testMiddleWareIsDispatchedInCorrectOrder()
    {
        [$statusCode, $body, $headers] = fetchUri(
            'http://local.slimauryn.basereality.com/test_middleware/middleware_correct_order',
            'GET'
        );

        $expectedString = SingleRouteWithMessageMiddleware::HEADER_NAME . ': 1st, 2nd, 3rd';
        $this->assertContains($expectedString, $headers);
    }

    public function providesAdditionDepdendencyInjectionIsDoneCorrectly()
    {
        return [
            ['/test_di/interface_is_aliased', self::INTERFACE_FOO_WAS_ALIASED_TO_STANDARD_FOO],
            ['/test_di/interface_is_unaliased', self::INTERFACE_FOO_CANNOT_BE_MADE_NO_INSTRUCTIONS_FOR_IT],
            ['/test_di/interface_is_delegated', self::INTERFACE_FOO_CREATED_THROUGH_DELEGATION],
        ];
    }

    /**
     * @dataProvider providesAdditionDepdendencyInjectionIsDoneCorrectly
     */
    public function testAdditionDepdendencyInjectionIsDoneCorrectly($path, $message)
    {
        [$statusCode, $body, $headers] = fetchUri(
            'http://local.slimauryn.basereality.com' . $path,
            'GET'
        );

        $this->assertEquals(200, $statusCode);
        $data = json_decode($body, true);
        $this->assertSame(0, json_last_error());

        $this->assertArrayHasKey('di', $data);
        $this->assertSame($data['di'], $message);
    }
}
