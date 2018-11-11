<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\ValueInput;

class PatchTest extends BaseTestCase
{
    public function testExtractingName()
    {
        $json = <<< JSON
[
    { "op": "replace", "path": "/name", "value": "some updated name" }
]
JSON;
        $input = new ValueInput(json_decode($json, true));
        $updateWatchlistParams = PatchNameParams::createFromInput($input);
        $name = $updateWatchlistParams->getName();

        $this->assertEquals("some updated name", $name);
    }
}
