<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Danack\Console\Tests\Tester;

use Danack\Console\Application;
use Danack\Console\Output\Output;
use Danack\Console\Tester\ApplicationTester;
use Auryn\Provider;
use Danack\Console\Command\ParsedCommand;

class ApplicationTesterTest extends \PHPUnit_Framework_TestCase
{
    protected $application;
    
    /** @var  ApplicationTester */
    protected $tester;

    /** @var  ParsedCommand */
    protected $parsedCommand;

    /** @var  Provider */
    protected $provider;
    
    protected function setUp()
    {
        $this->application = new Application();
        $this->application->setAutoExit(false);
        $callable = function (Output $output) { 
            $output->writeln('foo'); 
        };
        $this->application->register('foo', $callable)
            ->addArgument('foo');

        $this->tester = new ApplicationTester($this->application);
        $this->provider = new Provider();

        $this->parsedCommand = $this->tester->run(array('command' => 'foo', 'foo' => 'bar'), array('interactive' => false, 'decorated' => false, 'verbosity' => Output::VERBOSITY_VERBOSE));
    }

    protected function tearDown()
    {
        $this->application = null;
        $this->tester = null;
    }

    public function testRun()
    {
        $this->assertFalse($this->tester->getInput()->isInteractive(), '->execute() takes an interactive option');
        $this->assertFalse($this->tester->getOutput()->isDecorated(), '->execute() takes a decorated option');
        $this->assertEquals(Output::VERBOSITY_VERBOSE, $this->tester->getOutput()->getVerbosity(), '->execute() takes a verbosity option');
    }

    public function testGetInput()
    {
        $this->assertEquals('bar', $this->tester->getInput()->getArgument('foo'), '->getInput() returns the current input instance');
    }

    public function testGetOutput()
    {
        $output = $this->tester->getOutput();
        $this->provider->alias(
            'Danack\Console\Output\Output',
            get_class($output)
        );

        $this->provider->share($output);
        $callable = $this->parsedCommand->getCallable();
        $this->provider->execute($callable, []);
        
        rewind($this->tester->getOutput()->getStream());
        $contents = stream_get_contents($this->tester->getOutput()->getStream());
        $this->assertEquals('foo'.PHP_EOL, $contents, '->getOutput() returns the current output instance');
    }

    public function testGetDisplay()
    {
        $output = $this->tester->getOutput();
        $this->provider->alias(
            'Danack\Console\Output\Output',
            get_class($output)
        );

        $this->provider->share($output);
        $callable = $this->parsedCommand->getCallable();
        $this->provider->execute($callable, []);
        
        $this->assertEquals('foo'.PHP_EOL, $this->tester->getDisplay(), '->getDisplay() returns the display of the last execution');
    }
}
