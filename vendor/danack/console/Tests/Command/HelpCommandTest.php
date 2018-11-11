<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Danack\Console\Tests\Command;

use Danack\Console\Tester\CommandTester;
use Danack\Console\Command\HelpCommand;
use Danack\Console\Command\ListCommand;
use Danack\Console\Application;
use Auryn\Provider;

class HelpCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteForCommandAlias()
    {
        $command = new HelpCommand();
        $command->setApplication(new Application());
        $commandTester = new CommandTester($command);
        $parsedCommand = $commandTester->execute(array('command_name' => 'li'));
        $provider = new Provider();
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertRegExp('/list \[--xml\] \[--raw\] \[--format="\.\.\."\] \[namespace\]/', $commandTester->getDisplay(), '->execute() returns a text help for the given command alias');
    }

    public function testExecuteForCommand()
    {
        $command = new HelpCommand();
        $commandTester = new CommandTester($command);
        $command->setCommand(new ListCommand());
        $parsedCommand = $commandTester->execute(array());
        $provider = new Provider();
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertRegExp('/list \[--xml\] \[--raw\] \[--format="\.\.\."\] \[namespace\]/', $commandTester->getDisplay(), '->execute() returns a text help for the given command');
    }

    public function testExecuteForCommandWithXmlOption()
    {
        $command = new HelpCommand();
        $commandTester = new CommandTester($command);
        $command->setCommand(new ListCommand());
        $parsedCommand = $commandTester->execute(array('--format' => 'xml'));
        $provider = new Provider();
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertRegExp('/<command/', $commandTester->getDisplay(), '->execute() returns an XML help text if --xml is passed');
    }

    public function testExecuteForApplicationCommand()
    {
        $application = new Application();
        $commandTester = new CommandTester($application->get('help'));
        $parsedCommand = $commandTester->execute(array('command_name' => 'list'));
        $provider = new Provider();
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertRegExp('/list \[--xml\] \[--raw\] \[--format="\.\.\."\] \[namespace\]/', $commandTester->getDisplay(), '->execute() returns a text help for the given command');
    }

    public function testExecuteForApplicationCommandWithXmlOption()
    {
        $application = new Application();
        $commandTester = new CommandTester($application->get('help'));
        $parsedCommand = $commandTester->execute(array('command_name' => 'list', '--format' => 'xml'));
        $provider = new Provider();
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertRegExp('/list \[--xml\] \[--raw\] \[--format="\.\.\."\] \[namespace\]/', $commandTester->getDisplay(), '->execute() returns a text help for the given command');
        $this->assertRegExp('/<command/', $commandTester->getDisplay(), '->execute() returns an XML help text if --format=xml is passed');
    }
}
