<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\ValueInput;

/**
 * @coversNothing
 */
class PatchTest extends BaseTestCase
{
    /**
     * @covers \ParamsTest\PatchNameParams::getName
     */
    public function testExtractingName()
    {
        $json = <<< JSON
[
    { "op": "replace", "path": "/name", "value": "some updated name" }
]
JSON;
        $input = new ValueInput(json_decode($json, true));
        $params = PatchNameParams::createFromInput($input);
        $name = $params->getName();

        $this->assertEquals("some updated name", $name);
    }
}
