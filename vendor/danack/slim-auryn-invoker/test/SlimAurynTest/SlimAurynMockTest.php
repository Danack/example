<?php

declare(strict_types=1);

namespace SlimAurynTest;

use Auryn\Injector;
use SlimAuryn\InjectionParams;
use Mockery;


class SlimAurynMockTest extends BaseTestCase
{
    public function testDefineParams()
    {
        $shares = ['share1'];
        $aliases = ['interface' => 'class'];
        $delegates = ['className' => 'delegate'];
        $classParams = ['className' => [':paramName' => 'value']];
        $prepares = ['className' => 'prepareCallable'];
        $namedParams = ['paramName' => 'value'];

        $injectionParams = new InjectionParams(
            $shares,
            $aliases,
            $delegates,
            $classParams,
            $prepares,
            $namedParams
        );

        $mock = Mockery::mock('Auryn\Injector');
        $mock->shouldReceive('share')->withArgs(['share1']);
        $mock->shouldReceive('alias')->withArgs(['interface', 'class']);
        $mock->shouldReceive('delegate')->withArgs(['className', 'delegate']);
        $mock->shouldReceive('define')->withArgs(['className', [':paramName' => 'value']]);
        $mock->shouldReceive('prepare')->withArgs(['className', 'prepareCallable']);
        $mock->shouldReceive('defineParam')->withArgs(['paramName', 'value']);

        $injectionParams->addToInjector($mock);

        \Mockery::close();
    }

    public function testFromSharedObjects()
    {
        $injectionParams = InjectionParams::fromSharedObjects([
            'SlimAurynTest\Foo' => FooImplementation::create(),
            'SlimAurynTest\Bar' => Bar::create(),
        ]);

        $fn = function (Foo $foo, Bar $bar) {
            $this->assertInstanceOf('SlimAurynTest\FooImplementation', $foo);
            $this->assertInstanceOf('SlimAurynTest\Bar', $bar);
        };

        $injector = new Injector();
        $injectionParams->addToInjector($injector);
        $injector->execute($fn);
    }

    public function testMergeSharedObjects()
    {
        $injectionParams = new InjectionParams(
            [],
            ['SlimAurynTest\Foo' => 'SlimAurynTest\FooImplementation']
        );
        $injectionParams->mergeSharedObjects([
            'SlimAurynTest\Foo' => FooImplementation::create(),
            'SlimAurynTest\Quux' => QuuxImplementation::create()
        ]);

        $fn = function (Foo $foo, Quux $quux /*, Bar $bar */) {
            $this->assertInstanceOf('SlimAurynTest\FooImplementation', $foo);
            $this->assertInstanceOf('SlimAurynTest\Quux', $quux);
        };

        $injector = new Injector();
        $injectionParams->addToInjector($injector);
        $injector->execute($fn);
    }

    public function testMergeSharedObjectsCoverage()
    {
        $injectionParams = new InjectionParams();
        $injectionParams->mergeSharedObjects([
            'SlimAurynTest\Quux' => QuuxImplementation::create(),

        ]);

        $fn = function (Quux $quux) {
            $this->assertInstanceOf('SlimAurynTest\Quux', $quux);
        };

        $injector = new Injector();
        $injectionParams->addToInjector($injector);
        $injector->execute($fn);
    }

    public function testMergeSharedObjectsSharingImplementation()
    {
        $injectionParams = new InjectionParams();
        $injectionParams->mergeSharedObjects([
            'AurynConfigTest\Bar' => Bar::create(),
        ]);

        $injector = new Injector();
        $injectionParams->addToInjector($injector);
    }

    public function testMergeSharedObjectsExistingSharedPreserved()
    {
        $injectionParams = new InjectionParams(
            [FooImplementation::create()],
            ['SlimAurynTest\Foo' => 'SlimAurynTest\FooImplementation']
        );
        $injectionParams->mergeSharedObjects([
        ]);

        $injector = new Injector();
        $injectionParams->addToInjector($injector);

        $fn = function (Foo $foo) {
            $this->assertInstanceOf('SlimAurynTest\Foo', $foo);
        };

        $injector = new Injector();
        $injectionParams->addToInjector($injector);
        $injector->execute($fn);
    }

    public function testMergeSharedObjectsError()
    {
        $injectionParams = new InjectionParams();
        $this->expectException('Auryn\InjectorException');
        $injectionParams->mergeSharedObjects([
            'SlimAurynTest\Bar' => 'hello',
        ]);
    }

    public function testMergeSharedObjectsError2()
    {
        $injectionParams = new InjectionParams(
            [],
            ['SlimAurynTest\Foo' => 'SlimAurynTest\FooImplementation']
        );
        $this->expectException('Auryn\InjectorException');
        $injectionParams->mergeSharedObjects([
            'SlimAurynTest\Foo' => 'hello',
        ]);
    }
}