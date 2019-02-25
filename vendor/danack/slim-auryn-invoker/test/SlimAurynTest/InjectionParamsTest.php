<?php

namespace SlimAurynTest;

use Auryn\Injector;
use Auryn\InjectorException;
use SlimAurynTest\BaseTestCase;
use SlimAuryn\RouteParams;
use SlimAuryn\RouteParamsException;

use SlimAurynTest\Foo;
use SlimAurynTest\FooPublicConstructor;
use SlimAurynTest\BasicStringValue;
use SlimAuryn\InjectionParams;
use SlimAurynTest\StringValue;
use SlimAurynTest\MutableStringValue;

class InjectionParamsTest extends BaseTestCase
{
    public function testInjectionParams_alias()
    {
        $injector = new Injector();
        $injectionParams = new InjectionParams();
        $injectionParams->alias(Foo::class, FooPublicConstructor::class);
        $injectionParams->addToInjector($injector);
        $obj = $injector->make(Foo::class);
        $this->assertInstanceOf(FooPublicConstructor::class, $obj);
    }

    public function testInjectionParams_share()
    {
        $injector = new Injector();
        $injectionParams = new InjectionParams();
        $injectionParams->alias(StringValue::class, BasicStringValue::class);

        $testString = 'testing';
        $instance = new BasicStringValue($testString);
        $injectionParams->share($instance);

        $injectionParams->addToInjector($injector);
        $obj = $injector->make(StringValue::class);
        $this->assertSame($testString, $obj->getString());
    }

    public function testInjectionParams_delegate()
    {
        $injector = new Injector();
        $injectionParams = new InjectionParams();
        $injectionParams->alias(StringValue::class, MutableStringValue::class);

        $testString = 'testing';
        $createFn = function () use ($testString) {
            return new MutableStringValue($testString);
        };

        $injectionParams->delegate(MutableStringValue::class, $createFn);
        $injectionParams->addToInjector($injector);
        $obj = $injector->make(StringValue::class);
        $this->assertSame($testString, $obj->getString());
    }

    public function testInjectionParams_prepare()
    {
        $injector = new Injector();
        $injectionParams = new InjectionParams();
        $injectionParams->alias(StringValue::class, MutableStringValue::class);

        $testString = 'testing';
        $updatedString = 'this is an updated string';

        $createFn = function () use ($testString) {
            return new MutableStringValue($testString);
        };

        $prepareFn = function (MutableStringValue $mutableStringValue) use ($updatedString) {
            $mutableStringValue->setStringValue($updatedString);
        };


        $injectionParams->delegate(MutableStringValue::class, $createFn);
        $injectionParams->prepare(MutableStringValue::class, $prepareFn);

        $injectionParams->addToInjector($injector);
        $obj = $injector->make(StringValue::class);
        $this->assertSame($updatedString, $obj->getString());
    }

    public function testInjectionParams_defineNamedParam()
    {
        $injector = new Injector();
        $injectionParams = new InjectionParams();
        $message = 'Hello world';
        $injectionParams->defineNamedParam('message', $message);
        $fn = function ($message) {
            return $message;
        };

        $injectionParams->addToInjector($injector);

        $result = $injector->execute($fn);
        $this->assertSame($message, $result);
    }

    public function testInjectionParams_defineClassParam()
    {
        $injector = new Injector();
        $injectionParams = new InjectionParams();
        $injectionParams->alias(StringValue::class, BasicStringValue::class);

        $testString = 'testing';

        $injectionParams->defineClassParam(
            BasicStringValue::class,
            [':stringValue' => $testString]
        );

        $injectionParams->addToInjector($injector);
        $obj = $injector->make(StringValue::class);
        $this->assertSame($testString, $obj->getString());
    }

    public function testBadSharedObjectsGivesException()
    {
        $sharedObjects = [0 => new \StdClass];

        $injectionParams = new InjectionParams();

        $this->expectException(InjectorException::class);
        $this->expectExceptionMessage('sharedObjects must be a string indexed array');

        $injectionParams->mergeSharedObjects($sharedObjects);
    }
}
