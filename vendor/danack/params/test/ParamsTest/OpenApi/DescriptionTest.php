<?php

declare(strict_types=1);

namespace ParamsTest\OpenApi;

use Params\OpenApi\ShouldNeverBeCalledParamDescription;
use Params\OpenApi\StandardParamDescription;
use Params\Rule\Enum;
use Params\Rule\GetInt;
use Params\Rule\GetIntOrDefault;
use Params\Rule\GetOptionalInt;
use Params\Rule\GetOptionalString;
use Params\Rule\GetString;
use Params\Rule\GetStringOrDefault;
use Params\Rule\MaxIntValue;
use Params\Rule\MaxLength;
use Params\Rule\MinIntValue;
use Params\Rule\MinLength;
use Params\Rule\MultipleEnum;
use Params\Rule\NotNull;
use Params\Rule\PositiveInt;
use Params\Rule\SkipIfNull;
use Params\Rule\Trim;
use Params\Rule\ValidDate;
use Params\Rule\ValidDatetime;
use ParamsTest\BaseTestCase;
use VarMap\ArrayVarMap;
use Params\Rule\AlwaysEndsRule;
use Params\Exception\OpenApiException;

// OpenApi 3 spec
// https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md
function getParamDescriptionFromRules($allRules)
{
    $ruleDescriptions = [];

    foreach ($allRules as $name => $rules) {
        $description = new StandardParamDescription();

        $description->setName($name);

        foreach ($rules as $rule) {
            /** @var $rule \Params\Rule */
            $rule->updateParamDescription($description);
        }

        $ruleDescriptions[] = $description->toArray();
    }

    return $ruleDescriptions;
}

class DescriptionTest extends BaseTestCase
{
    public function testEnum()
    {
        $values = [
            'available',
            'pending',
            'sold'
        ];
        $schemaExpectations = [
            'enum' => $values,
        ];
        $varMap = new ArrayVarMap([]);
        $rules =  [
            'value' => [
                new GetString($varMap),
                new Enum($values),
            ],
        ];
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testRequired()
    {
        $descriptionExpectations = [
            'required' => true,
        ];
        $varMap = new ArrayVarMap([]);
        $rules = RequiredStringExample::getRules($varMap);
        $this->performFullTest([], $descriptionExpectations, $rules);
    }

    public function testMinLength()
    {
        $schemaExpectations = [
            'minLength' => RequiredStringExample::MIN_LENGTH,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = RequiredStringExample::getRules($varMap);
        $this->performSchemaTest($schemaExpectations, $rules);
    }





    public function testMaxLength()
    {
        $schemaExpectations = [
            'maxLength' => RequiredStringExample::MAX_LENGTH,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = RequiredStringExample::getRules($varMap);
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testInt()
    {
        $descriptionExpectations = [
            'required' => true
        ];

        $schemaExpectations = [
            'type' => 'integer'
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetInt($varMap)
            ],
        ];

        $this->performFullTest($schemaExpectations, $descriptionExpectations, $rules);
    }

    public function testIntOrDefault()
    {
        $default = 5;
        $schemaExpectations = [
            'type' => 'integer',
            'default' => $default
        ];
        $paramExpectations = [
            'required' => false,
        ];
        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetIntOrDefault($default, $varMap)
            ],
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testStringOrDefault()
    {
        $default = 'foo';
        $paramExpectations = [
            'required' => false,
        ];
        $schemaExpectations = [
            'type' => 'string',
            'default' => $default
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetStringOrDefault($default, $varMap)
            ],
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testOptionalInt()
    {
        $paramExpectations = [
            'required' => false,
        ];
        $schemaExpectations = [
            'type' => 'integer'
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetOptionalInt($varMap)
            ],
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testOptionalString()
    {
        $paramExpectations = [
            'required' => false,
        ];
        $schemaExpectations = [
            'type' => 'string'
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetOptionalString($varMap)
            ],
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testMinInt()
    {
        $maxValue = 10;
        $schemaExpectations = [
            'minimum' => $maxValue,
            'exclusiveMinimum' => false
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetInt($varMap),
                new MinIntValue($maxValue)
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testMaximumLength()
    {
        $maxLength = 10;
        $schemaExpectations = [
            'maxLength' => $maxLength,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetString($varMap),
                new MaxLength($maxLength)
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function providesValidMinimumLength()
    {
        return [[1], [2], [100] ];
    }

    /**
     * @dataProvider providesValidMinimumLength
     */
    public function testMininumLength($minLength)
    {
        $schemaExpectations = [
            'minLength' => $minLength,
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetString($varMap),
                new MinLength($minLength)
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function providesInvalidMininumLength()
    {
        return [[0], [-1], [-2], [-3] ];
    }

    /**
     * @param $minLength
     * @dataProvider providesInvalidMininumLength
     */
    public function testInvalidMininumLength($minLength)
    {
        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetString($varMap),
                new MinLength($minLength)
            ],
        ];

        $this->expectException(OpenApiException::class);
        getParamDescriptionFromRules($rules);
    }


    public function providesInvalidMaximumLength()
    {
        return [[0], [-1] ];
    }

    /**
     * @param $maxLength
     * @dataProvider providesInvalidMaximumLength
     */
    public function testInvalidMaximumLength($maxLength)
    {
        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetString($varMap),
                new MaxLength($maxLength)
            ],
        ];

        $this->expectException(OpenApiException::class);
        getParamDescriptionFromRules($rules);
    }

    public function providesValidMaximumLength()
    {
        return [[1], [2], [100] ];
    }

    /**
     * @param $maxLength
     * @dataProvider providesValidMaximumLength
     */
    public function testValidMaximumLength($maxLength)
    {
        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetString($varMap),
                new MaxLength($maxLength)
            ],
        ];

        $schemaExpectations = [
            'maxLength' => $maxLength,
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testEmptySchema()
    {
        $description = new StandardParamDescription();
        $description->setName('testing');
        $result = $description->toArray();
        $this->assertEquals(['name' => 'testing'], $result);
    }

    public function testMaxInt()
    {
        $maxValue = 45;
        $schemaExpectations = [
            'maximum' => $maxValue,
            'exclusiveMaximum' => false
        ];

        $varMap = new ArrayVarMap([]);
        $rules = [
            'value' => [
                new GetInt($varMap),
                new MaxIntValue($maxValue)
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testPositiveInt()
    {
        $schemaExpectations = [
            'minimum' => 0,
            'exclusiveMinimum' => false,
            'type' => 'integer'
        ];

        $rules = [
            'value' => [
                new PositiveInt(),
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testSkipIfNull()
    {
        $schemaExpectations = [
            'nullable' => true
        ];
        $rules = [
            'value' => [
                new SkipIfNull()
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testValidDate()
    {
        $schemaExpectations = [
            'type' => 'string',
            'format' => 'date'
        ];
        $rules = [
            'value' => [
                new ValidDate()
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testValidDateTime()
    {
        $schemaExpectations = [
            'type' => 'string',
            'format' => 'date-time'
        ];
        $rules = [
            'value' => [
                new ValidDatetime()
            ],
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }


    private function performSchemaTest($schemaExpectations, $rules)
    {
        $paramDescription = getParamDescriptionFromRules($rules);

        $this->assertCount(1, $paramDescription);
        $statusDescription = $paramDescription[0];

        $this->assertArrayHasKey('schema', $statusDescription);
        $schema = $statusDescription['schema'];

        foreach ($schemaExpectations as $key => $value) {
            $this->assertArrayHasKey($key, $schema, "Schema missing key [$key]. Schema is " .json_encode($schema));
            $this->assertEquals($value, $schema[$key]);
        }
    }


    private function performFullTest($schemaExpectations, $paramExpectations, $rules)
    {
        $paramDescription = getParamDescriptionFromRules($rules);

        $this->assertCount(1, $paramDescription);
        $openApiDescription = $paramDescription[0];

        $this->assertArrayHasKey('schema', $openApiDescription);
        $schema = $openApiDescription['schema'];

        foreach ($schemaExpectations as $key => $value) {
            $this->assertArrayHasKey($key, $schema, "Schema missing key [$key]. Schema is " .json_encode($schema));
            $this->assertEquals($value, $schema[$key]);
        }

        foreach ($paramExpectations as $key => $value) {
            $this->assertArrayHasKey($key, $openApiDescription, "openApiDescription missing key [$key]. Description is " .json_encode($openApiDescription));
            $this->assertEquals($value, $openApiDescription[$key]);
        }
    }

    public function testNonStringEnumThrows()
    {
        $description = new StandardParamDescription();
        $this->expectException(OpenApiException::class);
        $description->setEnum(['foo', 5]);
    }

    /**
     *
     */
    public function testCoverageOnly()
    {
        $description = new ShouldNeverBeCalledParamDescription();
        $trimRule = new Trim();
        $trimRule->updateParamDescription($description);

        $notNullRule = new NotNull();
        $notNullRule->updateParamDescription($description);

        $alwaysEndsRule = new AlwaysEndsRule(5);
        $alwaysEndsRule->updateParamDescription($description);
    }
}
