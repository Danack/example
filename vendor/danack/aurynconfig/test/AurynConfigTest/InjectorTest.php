<?php


namespace AurynConfigTest;

use Auryn\Injector;
use AurynConfig\InjectionParams;
use Mockery;

class InjectorTest extends BaseTestCase
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
    }

    public function testFromSharedObjects()
    {
        $injectionParams = InjectionParams::fromSharedObjects([
            'AurynConfigTest\Foo' => FooImplementation::create(),
            'AurynConfigTest\Bar' => Bar::create(),
        ]);
        
        $fn = function (Foo $foo, Bar $bar) {
            $this->assertInstanceOf('AurynConfigTest\FooImplementation', $foo);
            $this->assertInstanceOf('AurynConfigTest\Bar', $bar);
        };

        $injector = new Injector();
        $injectionParams->addToInjector($injector);
        $injector->execute($fn);
    }

    public function testMergeSharedObjects()
    {
        $injectionParams = new InjectionParams(
            [],
            ['AurynConfigTest\Foo' => 'AurynConfigTest\FooImplementation']
        );
        $injectionParams->mergeSharedObjects([
            'AurynConfigTest\Foo' => FooImplementation::create(),
            'AurynConfigTest\Quux' => QuuxImplementation::create()
        ]);
        
        $fn = function (Foo $foo, Quux $quux /*, Bar $bar */) {
            $this->assertInstanceOf('AurynConfigTest\FooImplementation', $foo);
            $this->assertInstanceOf('AurynConfigTest\Quux', $quux);
        };

        $injector = new Injector();
        $injectionParams->addToInjector($injector);
        $injector->execute($fn);
    }

    public function testMergeSharedObjectsCoverage()
    {
        $injectionParams = new InjectionParams();
        $injectionParams->mergeSharedObjects([
            'AurynConfigTest\Quux' => QuuxImplementation::create(),
            
        ]);
        
        $fn = function (Quux $quux) {
            $this->assertInstanceOf('AurynConfigTest\Quux', $quux);
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
            ['AurynConfigTest\Foo' => 'AurynConfigTest\FooImplementation']
        );
        $injectionParams->mergeSharedObjects([
        ]);

        $injector = new Injector();
        $injectionParams->addToInjector($injector);
        
        $fn = function (Foo $foo) {
            $this->assertInstanceOf('AurynConfigTest\Foo', $foo);
        };

        $injector = new Injector();
        $injectionParams->addToInjector($injector);
        $injector->execute($fn);
    }

    public function testMergeSharedObjectsError()
    {
        $injectionParams = new InjectionParams();
        $this->setExpectedException('Auryn\InjectorException');
        $injectionParams->mergeSharedObjects([
            'AurynConfigTest\Bar' => 'hello',
        ]);
    }

    public function testMergeSharedObjectsError2()
    {
        $injectionParams = new InjectionParams(
            [],
            ['AurynConfigTest\Foo' => 'AurynConfigTest\FooImplementation']
        );
        $this->setExpectedException('Auryn\InjectorException');
        $injectionParams->mergeSharedObjects([
            'AurynConfigTest\Foo' => 'hello',
        ]);
    }
}
