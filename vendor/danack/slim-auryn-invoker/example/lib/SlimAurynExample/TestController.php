<?php

declare(strict_types=1);

namespace SlimAurynExample;

use Auryn\InjectionException;
use Auryn\Injector;
use SlimAuryn\Response\JsonResponse;
use SlimAurynTest\Foo\Foo;
use SlimAurynTest\IntegrationTest;

class TestController
{
    private function getHowFooIsMade(Injector $injector)
    {
        try {
            $foo = $injector->make(Foo::class);

            if ($foo->wasConstructedThroughDelegation() === true) {
                return IntegrationTest::INTERFACE_FOO_CREATED_THROUGH_DELEGATION;
            }

            if (is_a($foo, \SlimAurynTest\Foo\StandardFoo::class) === true) {
                return IntegrationTest::INTERFACE_FOO_WAS_ALIASED_TO_STANDARD_FOO;
            }
            return "Unsure how foo was created.";
        }
        catch (InjectionException $injectionException) {
            $stringToCheck = substr(Injector::M_NEEDS_DEFINITION, 0, 30);
            if (strpos($injectionException->getMessage(), $stringToCheck) === 0) {
                return IntegrationTest::INTERFACE_FOO_CANNOT_BE_MADE_NO_INSTRUCTIONS_FOR_IT;
            }

            return "Unsure how foo was created - but there was an exception: " . $injectionException->getMessage();
        }
    }


    public function testHowFooIsMade(Injector $injector)
    {
        $testMessage = $this->getHowFooIsMade($injector);

        return new JsonResponse(['di' => $testMessage]);
    }


    public function testMiddleware()
    {
        return new JsonResponse(['status' => 'Header should have been set by middleware']);
    }
}
