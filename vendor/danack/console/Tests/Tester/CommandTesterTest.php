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
use Danack\Console\Command\AbstractCommand;
use Danack\Console\Output\Output;
use Danack\Console\Tester\CommandTester;
use Danack\Console\Command\Command;
use Danack\Console\Command\ParsedCommand;
use Auryn\Provider;

class CommandTesterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Command */
    protected $command;
    
    /** @var  CommandTester */
    protected $tester;

    /** @var  ParsedCommand */
    protected $parsedCommand;

    /** @var  Provider */
    protected $provider;
    
    protected function setUp()
    {
        $callable = function (Output $output) { 
            $output->writeln('foo'); 
        };
        $this->command = new Command('foo', $callable);
        $this->command->addArgument('command');
        $this->command->addArgument('foo');
        $this->tester = new CommandTester($this->command);

        $this->provider = new Provider();


        $this->parsedCommand = $this->tester->execute(array('foo' => 'bar'), array('interactive' => false, 'decorated' => false, 'verbosity' => Output::VERBOSITY_VERBOSE));
    }

    protected function tearDown()
    {
        $this->command = null;
        $this->tester = null;
    }

    public function testExecute()
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
        $this->provider->alias(
            'Danack\Console\Output\Output',
            get_class($this->tester->getOutput())
        );

        $this->provider->share($this->tester->getOutput());
        
        $this->provider->execute($this->parsedCommand->getCallable(), []);
        rewind($this->tester->getOutput()->getStream());
        $this->assertEquals('foo'.PHP_EOL, stream_get_contents($this->tester->getOutput()->getStream()), '->getOutput() returns the current output instance');
    }

    public function testGetDisplay()
    {
        $this->provider->alias(
            'Danack\Console\Output\Output',
            get_class($this->tester->getOutput())
        );

        $this->provider->share($this->tester->getOutput());

        $callable = $this->parsedCommand->getCallable();
        $this->provider->execute($callable, []);
        $foo = $this->tester->getDisplay();
        $this->assertEquals('foo'.PHP_EOL, $foo, '->getDisplay() returns the display of the last execution');
    }


    public function testCommandFromApplication()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $callable = function ($input, $output) { $output->writeln('foo'); };
        $command = new Command('foo', $callable);
        
        $application->add($command);

        $tester = new CommandTester($application->find('foo'));
        $result = $tester->execute(array());
        $this->assertInstanceOf('Danack\Console\Command\ParsedCommand', $result);
    }
}
