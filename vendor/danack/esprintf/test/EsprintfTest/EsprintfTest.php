<?php

declare(strict_types=1);

namespace EsprintfTest;

use EsprintfTest\BaseTestCase;

class EsprintfTest extends BaseTestCase
{
    function testRaw()
    {
        $string = 'foo :raw_text bar';

        $params = [
            ':raw_text' => 'foo bar'
        ];

        $result = esprintf($string, $params);
        $this->assertEquals('foo foo bar bar', $result);
    }
}
