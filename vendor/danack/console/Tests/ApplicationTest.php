<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Danack\Console\Tests;

use Danack\Console\Application;
use Danack\Console\Helper\HelperSet;
use Danack\Console\Helper\FormatterHelper;
use Danack\Console\Input\ArgvInput;
use Danack\Console\Input\ArrayInput;
use Danack\Console\Input\InputInterface;
use Danack\Console\Input\InputArgument;
use Danack\Console\Input\InputDefinition;
use Danack\Console\Input\InputOption;
use Danack\Console\Output\NullOutput;
use Danack\Console\Output\Output;
use Danack\Console\Output\OutputInterface;
use Danack\Console\Output\StreamOutput;
use Danack\Console\Tester\ApplicationTester;
use Auryn\Provider;


class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixturesPath;

    public static function setUpBeforeClass()
    {
        self::$fixturesPath = realpath(__DIR__.'/Fixtures/');
        require_once self::$fixturesPath.'/FooCommand.php';
        require_once self::$fixturesPath.'/Foo1Command.php';
        require_once self::$fixturesPath.'/Foo2Command.php';
        require_once self::$fixturesPath.'/Foo3Command.php';
        require_once self::$fixturesPath.'/Foo4Command.php';
        require_once self::$fixturesPath.'/Foo5Command.php';
        require_once self::$fixturesPath.'/FoobarCommand.php';
        require_once self::$fixturesPath.'/BarBucCommand.php';
    }

    protected function normalizeLineBreaks($text)
    {
        return str_replace(PHP_EOL, "\n", $text);
    }

    /**
     * Replaces the dynamic placeholders of the command help text with a static version.
     * The placeholder %command.full_name% includes the script path that is not predictable
     * and can not be tested against.
     */
    protected function ensureStaticCommandHelp(Application $application)
    {
        foreach ($application->all() as $command) {
            $command->setHelp(str_replace('%command.full_name%', 'app/console %command.name%', $command->getHelp()));
        }
    }

    public function testConstructor()
    {
        $application = new Application('foo', 'bar');
        $this->assertEquals('foo', $application->getName(), '__construct() takes the application name as its first argument');
        $this->assertEquals('bar', $application->getVersion(), '__construct() takes the application version as its second argument');
        $this->assertEquals(array('help', 'list'), array_keys($application->all()), '__construct() registered the help and list commands by default');
    }

    public function testSetGetName()
    {
        $application = new Application();
        $application->setName('foo');
        $this->assertEquals('foo', $application->getName(), '->setName() sets the name of the application');
    }

    public function testSetGetVersion()
    {
        $application = new Application();
        $application->setVersion('bar');
        $this->assertEquals('bar', $application->getVersion(), '->setVersion() sets the version of the application');
    }

    public function testGetLongVersion()
    {
        $application = new Application('foo', 'bar');
        $this->assertEquals('<info>foo</info> version <comment>bar</comment>', $application->getLongVersion(), '->getLongVersion() returns the long version of the application');
    }

    public function testHelp()
    {
        $application = new Application();
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_gethelp.txt', $this->normalizeLineBreaks($application->getHelp()), '->setHelp() returns a help message');
    }

    public function testAll()
    {
        $application = new Application();
        $commands = $application->all();
        

        
        $this->assertInstanceOf('Danack\\Console\\Command\\HelpCommand', $commands['help'], '->all() returns the registered commands');

        $application->add(new \FooCommand());
        $commands = $application->all('foo');
        $this->assertCount(1, $commands, '->all() takes a namespace as its first argument');
    }

    public function testRegister()
    {
        $callable = function (){};
        $application = new Application();
        $command = $application->register('foo', $callable);
        $this->assertEquals('foo', $command->getName(), '->register() registers a new command');
    }

    public function testAdd()
    {
        $application = new Application();
        $application->add($foo = new \FooCommand());
        $commands = $application->all();
        $this->assertEquals($foo, $commands['foo:bar'], '->add() registers a command');

        $application = new Application();
        $application->addCommands(array($foo = new \FooCommand(), $foo1 = new \Foo1Command()));
        $commands = $application->all();
        $this->assertEquals(array($foo, $foo1), array($commands['foo:bar'], $commands['foo:bar1']), '->addCommands() registers an array of commands');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Command class "Foo5Command" is not correctly initialized. You probably forgot to call the parent constructor.
     */
    public function testAddCommandWithEmptyConstructor()
    {
        $application = new Application();
        $application->add(new \Foo5Command());
    }

    public function testHasGet()
    {
        $application = new Application();
        $this->assertTrue($application->has('list'), '->has() returns true if a named command is registered');
        $this->assertFalse($application->has('afoobar'), '->has() returns false if a named command is not registered');

        $application->add($foo = new \FooCommand());
        $this->assertTrue($application->has('afoobar'), '->has() returns true if an alias is registered');
        $this->assertEquals($foo, $application->get('foo:bar'), '->get() returns a command by name');
        $this->assertEquals($foo, $application->get('afoobar'), '->get() returns a command by alias');

        $application = new Application();
        $application->add($foo = new \FooCommand());
        // simulate --help
        $r = new \ReflectionObject($application);
        $p = $r->getProperty('wantHelps');
        $p->setAccessible(true);
        $p->setValue($application, true);
        $command = $application->get('foo:bar');
        $this->assertInstanceOf('Danack\Console\Command\HelpCommand', $command, '->get() returns the help command if --help is provided as the input');
    }

    public function testSilentHelp()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $tester = new ApplicationTester($application);
        $tester->run(array('-h' => true, '-q' => true), array('decorated' => false));

        $this->assertEmpty($tester->getDisplay(true));
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage The command "foofoo" does not exist.
     */
    public function testGetInvalidCommand()
    {
        $application = new Application();
        $application->get('foofoo');
    }

    public function testGetNamespaces()
    {
        $application = new Application();
        $application->add(new \FooCommand());
        $application->add(new \Foo1Command());
        $this->assertEquals(array('foo'), $application->getNamespaces(), '->getNamespaces() returns an array of unique used namespaces');
    }

    public function testFindNamespace()
    {
        $application = new Application();
        $application->add(new \FooCommand());
        $this->assertEquals('foo', $application->findNamespace('foo'), '->findNamespace() returns the given namespace if it exists');
        $this->assertEquals('foo', $application->findNamespace('f'), '->findNamespace() finds a namespace given an abbreviation');
        $application->add(new \Foo2Command());
        $this->assertEquals('foo', $application->findNamespace('foo'), '->findNamespace() returns the given namespace if it exists');
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage The namespace "f" is ambiguous (foo, foo1).
     */
    public function testFindAmbiguousNamespace()
    {
        $application = new Application();
        $application->add(new \BarBucCommand());
        $application->add(new \FooCommand());
        $application->add(new \Foo2Command());
        $application->findNamespace('f');
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage There are no commands defined in the "bar" namespace.
     */
    public function testFindInvalidNamespace()
    {
        $application = new Application();
        $application->findNamespace('bar');
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Command "foo1" is not defined
     */
    public function testFindUniqueNameButNamespaceName()
    {
        $application = new Application();
        $application->add(new \FooCommand());
        $application->add(new \Foo1Command());
        $application->add(new \Foo2Command());

        $application->find($commandName = 'foo1');
    }

    public function testFind()
    {
        $application = new Application();
        $application->add(new \FooCommand());

        $this->assertInstanceOf('FooCommand', $application->find('foo:bar'), '->find() returns a command if its name exists');
        $this->assertInstanceOf('Danack\Console\Command\HelpCommand', $application->find('h'), '->find() returns a command if its name exists');
        $this->assertInstanceOf('FooCommand', $application->find('f:bar'), '->find() returns a command if the abbreviation for the namespace exists');
        $this->assertInstanceOf('FooCommand', $application->find('f:b'), '->find() returns a command if the abbreviation for the namespace and the command name exist');
        $this->assertInstanceOf('FooCommand', $application->find('a'), '->find() returns a command if the abbreviation exists for an alias');
    }

    /**
     * @dataProvider provideAmbiguousAbbreviations
     */
    public function testFindWithAmbiguousAbbreviations($abbreviation, $expectedExceptionMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedExceptionMessage);

        $application = new Application();
        $application->add(new \FooCommand());
        $application->add(new \Foo1Command());
        $application->add(new \Foo2Command());

        $application->find($abbreviation);
    }

    public function provideAmbiguousAbbreviations()
    {
        return array(
            array('f', 'Command "f" is not defined.'),
            array('a', 'Command "a" is ambiguous (afoobar, afoobar1 and 1 more).'),
            array('foo:b', 'Command "foo:b" is ambiguous (foo:bar, foo:bar1 and 1 more).')
        );
    }

    public function testFindCommandEqualNamespace()
    {
        $application = new Application();
        $application->add(new \Foo3Command());
        $application->add(new \Foo4Command());

        $this->assertInstanceOf('Foo3Command', $application->find('foo3:bar'), '->find() returns the good command even if a namespace has same name');
        $this->assertInstanceOf('Foo4Command', $application->find('foo3:bar:toh'), '->find() returns a command even if its namespace equals another command name');
    }

    public function testFindCommandWithAmbiguousNamespacesButUniqueName()
    {
        $application = new Application();
        $application->add(new \FooCommand());
        $application->add(new \FoobarCommand());

        $this->assertInstanceOf('FoobarCommand', $application->find('f:f'));
    }

    public function testFindCommandWithMissingNamespace()
    {
        $application = new Application();
        $application->add(new \Foo4Command());

        $this->assertInstanceOf('Foo4Command', $application->find('f::t'));
    }

    /**
     * @dataProvider             provideInvalidCommandNamesSingle
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Did you mean this
     */
    public function testFindAlternativeExceptionMessageSingle($name)
    {
        $application = new Application();
        $application->add(new \Foo3Command());
        $application->find($name);
    }

    public function provideInvalidCommandNamesSingle()
    {
        return array(
            array('foo3:baR'),
            array('foO3:bar')
        );
    }

    public function testFindAlternativeExceptionMessageMultiple()
    {
        $application = new Application();
        $application->add(new \FooCommand());
        $application->add(new \Foo1Command());
        $application->add(new \Foo2Command());

        // Command + plural
        try {
            $application->find('foo:baR');
            $this->fail('->find() throws an \InvalidArgumentException if command does not exist, with alternatives');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->find() throws an \InvalidArgumentException if command does not exist, with alternatives');
            $this->assertRegExp('/Did you mean one of these/', $e->getMessage(), '->find() throws an \InvalidArgumentException if command does not exist, with alternatives');
            $this->assertRegExp('/foo1:bar/', $e->getMessage());
            $this->assertRegExp('/foo:bar/', $e->getMessage());
        }

        // Namespace + plural
        try {
            $application->find('foo2:bar');
            $this->fail('->find() throws an \InvalidArgumentException if command does not exist, with alternatives');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->find() throws an \InvalidArgumentException if command does not exist, with alternatives');
            $this->assertRegExp('/Did you mean one of these/', $e->getMessage(), '->find() throws an \InvalidArgumentException if command does not exist, with alternatives');
            $this->assertRegExp('/foo1/', $e->getMessage());
        }

        $application->add(new \Foo3Command());
        $application->add(new \Foo4Command());

        // Subnamespace + plural
        try {
            $a = $application->find('foo3:');
            $this->fail('->find() should throw an \InvalidArgumentException if a command is ambiguous because of a subnamespace, with alternatives');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e);
            $this->assertRegExp('/foo3:bar/', $e->getMessage());
            $this->assertRegExp('/foo3:bar:toh/', $e->getMessage());
        }
    }

    public function testFindAlternativeCommands()
    {
        $application = new Application();

        $application->add(new \FooCommand());
        $application->add(new \Foo1Command());
        $application->add(new \Foo2Command());

        try {
            $application->find($commandName = 'Unknown command');
            $this->fail('->find() throws an \InvalidArgumentException if command does not exist');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->find() throws an \InvalidArgumentException if command does not exist');
            $this->assertEquals(sprintf('Command "%s" is not defined.', $commandName), $e->getMessage(), '->find() throws an \InvalidArgumentException if command does not exist, without alternatives');
        }

        // Test if "bar1" command throw an "\InvalidArgumentException" and does not contain
        // "foo:bar" as alternative because "bar1" is too far from "foo:bar"
        try {
            $application->find($commandName = 'bar1');
            $this->fail('->find() throws an \InvalidArgumentException if command does not exist');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->find() throws an \InvalidArgumentException if command does not exist');
            $this->assertRegExp(sprintf('/Command "%s" is not defined./', $commandName), $e->getMessage(), '->find() throws an \InvalidArgumentException if command does not exist, with alternatives');
            $this->assertRegExp('/afoobar1/', $e->getMessage(), '->find() throws an \InvalidArgumentException if command does not exist, with alternative : "afoobar1"');
            $this->assertRegExp('/foo:bar1/', $e->getMessage(), '->find() throws an \InvalidArgumentException if command does not exist, with alternative : "foo:bar1"');
            $this->assertNotRegExp('/foo:bar(?>!1)/', $e->getMessage(), '->find() throws an \InvalidArgumentException if command does not exist, without "foo:bar" alternative');
        }
    }

    public function testFindAlternativeCommandsWithAnAlias()
    {
        $fooCommand = new \FooCommand();
        $fooCommand->setAliases(array('foo2'));

        $application = new Application();
        $application->add($fooCommand);

        $result = $application->find('foo');

        $this->assertSame($fooCommand, $result);
    }

    public function testFindAlternativeNamespace()
    {
        $application = new Application();

        $application->add(new \FooCommand());
        $application->add(new \Foo1Command());
        $application->add(new \Foo2Command());
        $application->add(new \foo3Command());

        try {
            $application->find('Unknown-namespace:Unknown-command');
            $this->fail('->find() throws an \InvalidArgumentException if namespace does not exist');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->find() throws an \InvalidArgumentException if namespace does not exist');
            $this->assertEquals('There are no commands defined in the "Unknown-namespace" namespace.', $e->getMessage(), '->find() throws an \InvalidArgumentException if namespace does not exist, without alternatives');
        }

        try {
            $application->find('foo2:command');
            $this->fail('->find() throws an \InvalidArgumentException if namespace does not exist');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->find() throws an \InvalidArgumentException if namespace does not exist');
            $this->assertRegExp('/There are no commands defined in the "foo2" namespace./', $e->getMessage(), '->find() throws an \InvalidArgumentException if namespace does not exist, with alternative');
            $this->assertRegExp('/foo/', $e->getMessage(), '->find() throws an \InvalidArgumentException if namespace does not exist, with alternative : "foo"');
            $this->assertRegExp('/foo1/', $e->getMessage(), '->find() throws an \InvalidArgumentException if namespace does not exist, with alternative : "foo1"');
            $this->assertRegExp('/foo3/', $e->getMessage(), '->find() throws an \InvalidArgumentException if namespace does not exist, with alternative : "foo3"');
        }
    }

    public function testFindNamespaceDoesNotFailOnDeepSimilarNamespaces()
    {
        $application = $this->getMock('Danack\Console\Application', array('getNamespaces'));
        $application->expects($this->once())
            ->method('getNamespaces')
            ->will($this->returnValue(array('foo:sublong', 'bar:sub')));

        $this->assertEquals('foo:sublong', $application->findNamespace('f:sub'));
    }


    public function testAsText()
    {
        $application = new Application();
        $application->add(new \FooCommand());
        $this->ensureStaticCommandHelp($application);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_astext1.txt', $this->normalizeLineBreaks($application->asText()), '->asText() returns a text representation of the application');
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_astext2.txt', $this->normalizeLineBreaks($application->asText('foo')), '->asText() returns a text representation of the application');
    }

    public function testAsXml()
    {
        $application = new Application();
        $application->add(new \FooCommand());
        $this->ensureStaticCommandHelp($application);
        $this->assertXmlStringEqualsXmlFile(self::$fixturesPath.'/application_asxml1.txt', $application->asXml(), '->asXml() returns an XML representation of the application');
        $this->assertXmlStringEqualsXmlFile(self::$fixturesPath.'/application_asxml2.txt', $application->asXml('foo'), '->asXml() returns an XML representation of the application');
    }

    public function testRenderException()
    {
        $application = $this->getMock('Danack\Console\Application', array('getTerminalWidth'));
        $application->setAutoExit(false);
        $application->expects($this->any())
            ->method('getTerminalWidth')
            ->will($this->returnValue(120));
        $tester = new ApplicationTester($application);

        $this->setExpectedException('InvalidArgumentException');

        $tester->run(array('command' => 'foo'), array('decorated' => false));
    }



    public function testRun()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->add($command = new \Foo1Command());
        $_SERVER['argv'] = array('cli.php', 'foo:bar1');

        ob_start();
        $application->parseCommandLine();
        ob_end_clean();

        $this->assertInstanceOf('Danack\Console\Input\ArgvInput', $command->input, '->run() creates an ArgvInput by default if none is given');
        $this->assertInstanceOf('Danack\Console\Output\ConsoleOutput', $command->output, '->run() creates a ConsoleOutput by default if none is given');

        $application = new Application();
        $application->setAutoExit(false);

        $this->ensureStaticCommandHelp($application);
        $tester = new ApplicationTester($application);

        $provider = new Provider();
        //TODO - this should be in a loop with data defined in an array
        
        $parsedCommand = $tester->run(array(), array('decorated' => false));
        $provider->execute($parsedCommand->getCallable(), []);
        
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run1.txt', $tester->getDisplay(true), '->run() runs the list command if no argument is passed');

        $parsedCommand = $tester->run(array('--help' => true), array('decorated' => false));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run2.txt', $tester->getDisplay(true), '->run() runs the help command if --help is passed');

        $parsedCommand = $tester->run(array('-h' => true), array('decorated' => false));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run2.txt', $tester->getDisplay(true), '->run() runs the help command if -h is passed');

        $parsedCommand = $tester->run(array('command' => 'list', '--help' => true), array('decorated' => false));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run3.txt', $tester->getDisplay(true), '->run() displays the help if --help is passed');

        $parsedCommand = $tester->run(array('command' => 'list', '-h' => true), array('decorated' => false));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run3.txt', $tester->getDisplay(true), '->run() displays the help if -h is passed');

        $parsedCommand = $tester->run(array('--ansi' => true));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertTrue($tester->getOutput()->isDecorated(), '->run() forces color output if --ansi is passed');

        $parsedCommand = $tester->run(array('--no-ansi' => true));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertFalse($tester->getOutput()->isDecorated(), '->run() forces color output to be disabled if --no-ansi is passed');

        $parsedCommand = $tester->run(array('--version' => true), array('decorated' => false));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run4.txt', $tester->getDisplay(true), '->run() displays the program version if --version is passed');

        $parsedCommand = $tester->run(array('-V' => true), array('decorated' => false));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertStringEqualsFile(self::$fixturesPath.'/application_run4.txt', $tester->getDisplay(true), '->run() displays the program version if -v is passed');

        $parsedCommand = $tester->run(array('command' => 'list', '--quiet' => true));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertSame('', $tester->getDisplay(), '->run() removes all output if --quiet is passed');

        $parsedCommand = $tester->run(array('command' => 'list', '-q' => true));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertSame('', $tester->getDisplay(), '->run() removes all output if -q is passed');

        $parsedCommand = $tester->run(array('command' => 'list', '--verbose' => true));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertSame(Output::VERBOSITY_VERBOSE, $tester->getOutput()->getVerbosity(), '->run() sets the output to verbose if --verbose is passed');

        $parsedCommand = $tester->run(array('command' => 'list', '--verbose' => 1));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertSame(Output::VERBOSITY_VERBOSE, $tester->getOutput()->getVerbosity(), '->run() sets the output to verbose if --verbose=1 is passed');

        $parsedCommand = $tester->run(array('command' => 'list', '--verbose' => 2));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertSame(Output::VERBOSITY_VERY_VERBOSE, $tester->getOutput()->getVerbosity(), '->run() sets the output to very verbose if --verbose=2 is passed');

        $parsedCommand = $tester->run(array('command' => 'list', '--verbose' => 3));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertSame(Output::VERBOSITY_DEBUG, $tester->getOutput()->getVerbosity(), '->run() sets the output to debug if --verbose=3 is passed');

        $parsedCommand = $tester->run(array('command' => 'list', '--verbose' => 4));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertSame(Output::VERBOSITY_VERBOSE, $tester->getOutput()->getVerbosity(), '->run() sets the output to verbose if unknown --verbose level is passed');

        $parsedCommand = $tester->run(array('command' => 'list', '-v' => true));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertSame(Output::VERBOSITY_VERBOSE, $tester->getOutput()->getVerbosity(), '->run() sets the output to verbose if -v is passed');

        $parsedCommand = $tester->run(array('command' => 'list', '-vv' => true));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertSame(Output::VERBOSITY_VERY_VERBOSE, $tester->getOutput()->getVerbosity(), '->run() sets the output to verbose if -v is passed');

        $parsedCommand = $tester->run(array('command' => 'list', '-vvv' => true));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertSame(Output::VERBOSITY_DEBUG, $tester->getOutput()->getVerbosity(), '->run() sets the output to verbose if -v is passed');

        $application = new Application();
        $application->setAutoExit(false);
        $application->add(new \FooCommand());
        $tester = new ApplicationTester($application);

        $parsedCommand = $tester->run(array('command' => 'foo:bar', '--no-interaction' => true), array('decorated' => false));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertSame('called'.PHP_EOL, $tester->getDisplay(), '->run() does not call interact() if --no-interaction is passed');

        $parsedCommand = $tester->run(array('command' => 'foo:bar', '-n' => true), array('decorated' => false));
        $provider->execute($parsedCommand->getCallable(), []);
        $this->assertSame('called'.PHP_EOL, $tester->getDisplay(), '->run() does not call interact() if -n is passed');
    }

    /**
     * Issue #9285
     *
     * If the "verbose" option is just before an argument in ArgvInput,
     * an argument value should not be treated as verbosity value.
     * This test will fail with "Not enough arguments." if broken
     */
    public function testVerboseValueNotBreakArguments()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->add(new \FooCommand());

        $output = new StreamOutput(fopen('php://memory', 'w', false));

        $input = new ArgvInput(array('cli.php', '-v', 'foo:bar'));
        $application->parseCommandLine($input, $output);

        $input = new ArgvInput(array('cli.php', '--verbose', 'foo:bar'));
        $application->parseCommandLine($input, $output);
    }

    /**
     * @expectedException \LogicException
     * @dataProvider getAddingAlreadySetDefinitionElementData
     */
    public function testAddingAlreadySetDefinitionElementData($def)
    {
        $code = function (InputInterface $input, OutputInterface $output) {};
        
        
        $application = new Application();
        $application->setAutoExit(false);
        $application
            ->register('foo', $code)
            ->setDefinition(array($def));

        $input = new ArrayInput(array('command' => 'foo'));
        $output = new NullOutput();
        $application->parseCommandLine($input, $output);
    }

    public function getAddingAlreadySetDefinitionElementData()
    {
        return array(
            array(new InputArgument('command', InputArgument::REQUIRED)),
            array(new InputOption('quiet', '', InputOption::VALUE_NONE)),
            array(new InputOption('query', 'q', InputOption::VALUE_NONE)),
        );
    }

    public function testGetDefaultHelperSetReturnsDefaultValues()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $helperSet = $application->getHelperSet();

        $this->assertTrue($helperSet->has('formatter'));
        $this->assertTrue($helperSet->has('dialog'));
        $this->assertTrue($helperSet->has('progress'));
    }

    public function testAddingSingleHelperSetOverwritesDefaultValues()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->setHelperSet(new HelperSet(array(new FormatterHelper())));

        $helperSet = $application->getHelperSet();

        $this->assertTrue($helperSet->has('formatter'));

        // no other default helper set should be returned
        $this->assertFalse($helperSet->has('dialog'));
        $this->assertFalse($helperSet->has('progress'));
    }

    public function testOverwritingDefaultHelperSetOverwritesDefaultValues()
    {
        $application = new CustomApplication();
        $application->setAutoExit(false);

        $application->setHelperSet(new HelperSet(array(new FormatterHelper())));

        $helperSet = $application->getHelperSet();

        $this->assertTrue($helperSet->has('formatter'));

        // no other default helper set should be returned
        $this->assertFalse($helperSet->has('dialog'));
        $this->assertFalse($helperSet->has('progress'));
    }

    public function testGetDefaultInputDefinitionReturnsDefaultValues()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $inputDefinition = $application->getDefinition();

        $this->assertTrue($inputDefinition->hasArgument('command'));

        $this->assertTrue($inputDefinition->hasOption('help'));
        $this->assertTrue($inputDefinition->hasOption('quiet'));
        $this->assertTrue($inputDefinition->hasOption('verbose'));
        $this->assertTrue($inputDefinition->hasOption('version'));
        $this->assertTrue($inputDefinition->hasOption('ansi'));
        $this->assertTrue($inputDefinition->hasOption('no-ansi'));
        $this->assertTrue($inputDefinition->hasOption('no-interaction'));
    }

    public function testOverwritingDefaultInputDefinitionOverwritesDefaultValues()
    {
        $application = new CustomApplication();
        $application->setAutoExit(false);

        $inputDefinition = $application->getDefinition();

        // check whether the default arguments and options are not returned any more
        $this->assertFalse($inputDefinition->hasArgument('command'));

        $this->assertFalse($inputDefinition->hasOption('help'));
        $this->assertFalse($inputDefinition->hasOption('quiet'));
        $this->assertFalse($inputDefinition->hasOption('verbose'));
        $this->assertFalse($inputDefinition->hasOption('version'));
        $this->assertFalse($inputDefinition->hasOption('ansi'));
        $this->assertFalse($inputDefinition->hasOption('no-ansi'));
        $this->assertFalse($inputDefinition->hasOption('no-interaction'));

        $this->assertTrue($inputDefinition->hasOption('custom'));
    }

    public function testSettingCustomInputDefinitionOverwritesDefaultValues()
    {
        $application = new Application();
        $application->setAutoExit(false);
        $application->setDefinition(new InputDefinition(array(new InputOption('--custom', '-c', InputOption::VALUE_NONE, 'Set the custom input definition.'))));

        $inputDefinition = $application->getDefinition();

        // check whether the default arguments and options are not returned any more
        $this->assertFalse($inputDefinition->hasArgument('command'));

        $this->assertFalse($inputDefinition->hasOption('help'));
        $this->assertFalse($inputDefinition->hasOption('quiet'));
        $this->assertFalse($inputDefinition->hasOption('verbose'));
        $this->assertFalse($inputDefinition->hasOption('version'));
        $this->assertFalse($inputDefinition->hasOption('ansi'));
        $this->assertFalse($inputDefinition->hasOption('no-ansi'));
        $this->assertFalse($inputDefinition->hasOption('no-interaction'));

        $this->assertTrue($inputDefinition->hasOption('custom'));
    }

    public function testTerminalDimensions()
    {
        $application = new Application();
        $originalDimensions = $application->getTerminalDimensions();
        $this->assertCount(2, $originalDimensions);

        $width = 80;
        if ($originalDimensions[0] == $width) {
            $width = 100;
        }

        $application->setTerminalDimensions($width, 80);
        $this->assertSame(array($width, 80), $application->getTerminalDimensions());
    }



    public function testSetRunCustomDefaultCommand()
    {
        $command = new \FooCommand();

        $application = new Application();
        $application->setAutoExit(false);
        $application->add($command);
        $application->setDefaultCommand($command->getName());
        $tester = new ApplicationTester($application);
        $parsedCommand = $tester->run(array());
        $provider = new Provider();
        $provider->execute($parsedCommand->getCallable(), []);
        
        $this->assertEquals('interact called'.PHP_EOL.'called'.PHP_EOL, $tester->getDisplay(), 'Application runs the default set command if different from \'list\' command');

        $application = new CustomDefaultCommandApplication();
        $application->setAutoExit(false);

        $tester = new ApplicationTester($application);
        $parsedCommand = $tester->run(array());
        $provider->execute($parsedCommand->getCallable(), []);

        $this->assertEquals('interact called'.PHP_EOL.'called'.PHP_EOL, $tester->getDisplay(), 'Application runs the default set command if different from \'list\' command');
    }
}

class CustomApplication extends Application
{
    /**
     * Overwrites the default input definition.
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        return new InputDefinition(array(new InputOption('--custom', '-c', InputOption::VALUE_NONE, 'Set the custom input definition.')));
    }

    /**
     * Gets the default helper set with the helpers that should always be available.
     *
     * @return HelperSet A HelperSet instance
     */
    protected function getDefaultHelperSet()
    {
        return new HelperSet(array(new FormatterHelper()));
    }
}

class CustomDefaultCommandApplication extends Application
{
    /**
     * Overwrites the constructor in order to set a different default command.
     */
    public function __construct()
    {
        parent::__construct();

        $command = new \FooCommand();
        $this->add($command);
        $this->setDefaultCommand($command->getName());
    }
}
