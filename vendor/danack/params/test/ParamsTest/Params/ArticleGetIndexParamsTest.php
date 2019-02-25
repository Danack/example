<?php

declare(strict_types=1);

namespace ParamsTest\Params;

use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use ParamsExample\GetArticlesParams;

/**
 * @coversNothing
 */
class ArticleGetIndexParamsTest extends BaseTestCase
{
//    public function testBasic()
//    {
//        $varMap = new ArrayVariableMap([]);
//        $articleGetIndexParams = ArticleGetIndexParams::fromVarMap($varMap);
//
//        $ordering = $articleGetIndexParams->getOrdering();
//
//        $expectedOrdering = [
//            'date' => Ordering::DESC
//        ];
//
//        $this->assertEquals($expectedOrdering, $ordering->toOrderArray());
//        $this->assertEquals(ArticleGetIndexParams::LIMIT_DEFAULT, $articleGetIndexParams->getLimit());
//        $this->assertEquals(null, $articleGetIndexParams->getAfterId());
//
//    }
//
//
//
//
//
//    public function testBasic2()
//    {
//        $after = 12345;
//        $limit = 123;
//
//        $varMap = new ArrayVariableMap([
//            'after' => (string)$after,
//            'limit' => (string)$limit
//        ]);
//
//        $articleGetIndexParams = ArticleGetIndexParams::fromVarMap($varMap);
//
//        $ordering = $articleGetIndexParams->getOrdering();
//
//        $expectedOrdering = [
//            'date' => Ordering::DESC
//        ];
//
//        $this->assertEquals($expectedOrdering, $ordering->toOrderArray());
//
//        $this->assertEquals($limit, $articleGetIndexParams->getLimit());
//        $this->assertEquals($after, $articleGetIndexParams->getAfterId());
//    }
}
