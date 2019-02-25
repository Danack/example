<?php

declare(strict_types=1);

namespace EsprintfTest;

use EsprintfTest\BaseTestCase;
use Esprintf\EsprintfException;

class EsprintfTest extends BaseTestCase
{
    public function testRaw()
    {
        $string = 'foo :raw_text bar';

        $params = [
            ':raw_text' => 'foo bar'
        ];

        $result = esprintf($string, $params);
        $this->assertEquals('foo foo bar bar', $result);
    }

    public function providesBadKeyGivesException()
    {
        $hasMissingKey = [
            ':html_text1' => 'foo bar',
            ':html_text2' => 'foo bar',
            'foo bar',
        ];

        $hasBadKey = [
            ':html_text1' => 'foo bar',
            ':html_text2' => 'foo bar',
            5 => 'foo bar',
        ];

        return [
            [0, 0, ['foo']],
            [2, 0, $hasMissingKey],
            [2, 5, $hasBadKey],
        ];
    }

    /**
     * @param $searchReplaceArray
     * @dataProvider providesBadKeyGivesException
     */
    public function testBadKeyGivesException($position, $badKey, $params)
    {
        $string = 'Irrelevant to test';
        $this->expectException(EsprintfException::class);

        $expectedMessage = sprintf(
            EsprintfException::KEY_IS_NOT_STRING,
            $position,
            $badKey
        );

        $this->expectExceptionMessage($expectedMessage);

        esprintf($string, $params);
    }

    function testUnknownEscaper()
    {
        $string = 'Irrelevant to test';

        $params = [
            ':hmtl_text' => 'foo bar' // typo in key
        ];

        $this->expectException(EsprintfException::class);

        $expectedMessage = sprintf(
            EsprintfException::UNKNOWN_ESCAPER_STRING,
            ':hmtl_text'
        );

        $this->expectExceptionMessage($expectedMessage);

        $result = esprintf($string, $params);
        $this->assertEquals('foo foo bar bar', $result);
    }
}
