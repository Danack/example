<?php

declare(strict_types=1);

namespace ExampleTest\ApiController;

use ExampleTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Example\Params\WordSearchParams;
use SlimAuryn\Response\JsonResponse;

class WordsTest extends BaseTestCase
{
    public function testSearchForWords()
    {
        $varMap = new ArrayVarMap([
            'search_string' => 'foob',
            'match_type' => WordSearchParams::MATCH_TYPE_BEGINS_WITH
        ]);
        $injector = createInjector([\VarMap\VarMap::class => $varMap]);

        $jsonResponse = $injector->execute('Example\ApiController\Words::searchForWords');

        $this->assertInstanceOf(JsonResponse::class, $jsonResponse);

        /** @var $jsonResponse \SlimAuryn\Response\JsonResponse */
        $this->assertEquals(200, $jsonResponse->getStatus());
        $data = json_decode_safe($jsonResponse->getBody());
        $this->assertSame(['FOOBAR'], $data);
    }
}
