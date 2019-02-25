<?php

declare(strict_types=1);

namespace ExampleTest\Repo\WordRepo;

use ExampleTest\BaseTestCase;
use Example\Repo\WordRepo\PdoWordRepo;
use Example\Params\WordSearchParams;

class PdoWordRepoTest extends BaseTestCase
{
    public function testExactMatch()
    {
        $pdoWordRepo = new PdoWordRepo(createPDO());
        $searchParams = new WordSearchParams(
            'foobar',
            WordSearchParams::MATCH_TYPE_EXACT,
            50
        );

        $words = $pdoWordRepo->searchForWords($searchParams);
        $this->assertEquals(['FOOBAR'], $words);

        $searchParams2 = new WordSearchParams(
            'xyzq',
            WordSearchParams::MATCH_TYPE_EXACT,
            50
        );

        $words2 = $pdoWordRepo->searchForWords($searchParams2);
        $this->assertSame([], $words2);
    }


    public function testBeginsWith()
    {
        $pdoWordRepo = new PdoWordRepo(createPDO());
        $searchParams = new WordSearchParams(
            'foob',
            WordSearchParams::MATCH_TYPE_BEGINS_WITH,
            50
        );

        $words = $pdoWordRepo->searchForWords($searchParams);
        $this->assertEquals(['FOOBAR'], $words);

        $searchParams2 = new WordSearchParams(
            'xyzq',
            WordSearchParams::MATCH_TYPE_BEGINS_WITH,
            50
        );

        $words2 = $pdoWordRepo->searchForWords($searchParams2);
        $this->assertSame([], $words2);
    }


    public function testEndsWith()
    {
        $pdoWordRepo = new PdoWordRepo(createPDO());
        $searchParams = new WordSearchParams(
            'raboo',
            WordSearchParams::MATCH_TYPE_ENDS_WITH,
            50
        );

        $words = $pdoWordRepo->searchForWords($searchParams);
        $this->assertEquals(['FOOBAR'], $words);

        $searchParams2 = new WordSearchParams(
            'xyzq',
            WordSearchParams::MATCH_TYPE_ENDS_WITH,
            50
        );

        $words2 = $pdoWordRepo->searchForWords($searchParams2);
        $this->assertSame([], $words2);
    }


    public function testLimit()
    {
        $pdoWordRepo = new PdoWordRepo(createPDO());
        $searchParams = new WordSearchParams(
            'tha',
            WordSearchParams::MATCH_TYPE_BEGINS_WITH,
            35
        );

        $words = $pdoWordRepo->searchForWords($searchParams);
        $this->assertCount(35, $words);
    }
}
