<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\Rule\ValidDatetime;

class ValidDatetimeTest extends BaseTestCase
{

    public function provideTestWorksCases()
    {
        return [
            [
                '2002-10-02T10:00:00-05:00',
                \DateTime::createFromFormat(\DateTime::RFC3339, '2002-10-02T10:00:00-05:00')
            ],
            [
                '2002-10-02T15:00:00Z',
                \DateTime::createFromFormat(\DateTime::RFC3339, '2002-10-02T15:00:00Z')
            ],

// This should work - but currently doesn't due to https://bugs.php.net/bug.php?id=75577
//            [
//                '2017-07-25T13:47:12.000+00:00',
//                \DateTime::createFromFormat(\DateTime::RFC3339_EXTENDED, '2017-07-25T13:47:12.000+00:00')
//            ],


            // TODO - this should be an allowed string - I think
            // '2002-10-02T15:00:00.05Z'

            // TODO - add support for 6 digit microseconds e.g. formatted by Golang
            // "Y-m-d\TH:i:s.uP", "2017-07-25T15:25:16.123456+02:00"
        ];
    }


    /**
     * @dataProvider provideTestWorksCases
     * @covers \Params\Rule\ValidDatetime
     */
    public function testValidationWorks($input, $expectedTime)
    {
        $validator = new ValidDatetime();
        $validationResult = $validator('foo', $input);

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedTime);
    }

    public function provideTestErrorsCases()
    {
        return [
            ['2pm on Tuesday'],
            ['Banana'],
        ];
    }

    /**
     * @dataProvider provideTestErrorsCases
     * @covers \Params\Rule\ValidDatetime
     */
    public function testValidationErrors($input)
    {
        $validator = new ValidDatetime();
        $validationResult = $validator('foo', $input);

        $this->assertNotNull($validationResult->getProblemMessage());
    }
}
