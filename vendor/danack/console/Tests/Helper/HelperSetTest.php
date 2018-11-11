<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Danack\Console\Tests\Helper;

use Danack\Console\Helper\HelperSet;
use Danack\Console\Command\AbstractCommand;
use Danack\Console\Command\Command;

class HelperSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Danack\Console\Helper\HelperSet::__construct
     */
    public function testConstructor()
    {
        $mock_helper = $this->getGenericMockHelper('fake_helper');
        $helperset = new HelperSet(array('fake_helper_alias' => $mock_helper));

        $this->assertEquals($mock_helper, $helperset->get('fake_helper_alias'), '__construct sets given helper to helpers');
        $this->assertTrue($helperset->has('fake_helper_alias'), '__construct sets helper alias for given helper');
    }

    /**
     * @covers \Danack\Console\Helper\HelperSet::set
     */
    public function testSet()
    {
        $helperset = new HelperSet();
        $helperset->set($this->getGenericMockHelper('fake_helper', $helperset));
        $this->assertTrue($helperset->has('fake_helper'), '->set() adds helper to helpers');

        $helperset = new HelperSet();
        $helperset->set($this->getGenericMockHelper('fake_helper_01', $helperset));
        $helperset->set($this->getGenericMockHelper('fake_helper_02', $helperset));
        $this->assertTrue($helperset->has('fake_helper_01'), '->set() will set multiple helpers on consecutive calls');
        $this->assertTrue($helperset->has('fake_helper_02'), '->set() will set multiple helpers on consecutive calls');

        $helperset = new HelperSet();
        $helperset->set($this->getGenericMockHelper('fake_helper', $helperset), 'fake_helper_alias');
        $this->assertTrue($helperset->has('fake_helper'), '->set() adds helper alias when set');
        $this->assertTrue($helperset->has('fake_helper_alias'), '->set() adds helper alias when set');
    }

    /**
     * @covers \Danack\Console\Helper\HelperSet::has
     */
    public function testHas()
    {
        $helperset = new HelperSet(array('fake_helper_alias' => $this->getGenericMockHelper('fake_helper')));
        $this->assertTrue($helperset->has('fake_helper'), '->has() finds set helper');
        $this->assertTrue($helperset->has('fake_helper_alias'), '->has() finds set helper by alias');
    }

    /**
     * @covers \Danack\Console\Helper\HelperSet::get
     */
    public function testGet()
    {
        $helper_01 = $this->getGenericMockHelper('fake_helper_01');
        $helper_02 = $this->getGenericMockHelper('fake_helper_02');
        $helperset = new HelperSet(array('fake_helper_01_alias' => $helper_01, 'fake_helper_02_alias' => $helper_02));
        $this->assertEquals($helper_01, $helperset->get('fake_helper_01'), '->get() returns correct helper by name');
        $this->assertEquals($helper_01, $helperset->get('fake_helper_01_alias'), '->get() returns correct helper by alias');
        $this->assertEquals($helper_02, $helperset->get('fake_helper_02'), '->get() returns correct helper by name');
        $this->assertEquals($helper_02, $helperset->get('fake_helper_02_alias'), '->get() returns correct helper by alias');

        $helperset = new HelperSet();
        try {
            $helperset->get('foo');
            $this->fail('->get() throws \InvalidArgumentException when helper not found');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->get() throws \InvalidArgumentException when helper not found');
            $this->assertContains('The helper "foo" is not defined.', $e->getMessage(), '->get() throws \InvalidArgumentException when helper not found');
        }
    }

    /**
     * @covers \Danack\Console\Helper\HelperSet::setCommand
     */
    public function testSetCommand()
    {
        $callable = function() {};
        $cmd_01 = new Command('foo', $callable);
        $cmd_02 = new Command('bar', $callable);

        $helperset = new HelperSet();
        $helperset->setCommand($cmd_01);
        $this->assertEquals($cmd_01, $helperset->getCommand(), '->setCommand() stores given command');

        $helperset = new HelperSet();
        $helperset->setCommand($cmd_01);
        $helperset->setCommand($cmd_02);
        $this->assertEquals($cmd_02, $helperset->getCommand(), '->setCommand() overwrites stored command with consecutive calls');
    }

    /**
     * @covers \Danack\Console\Helper\HelperSet::getCommand
     */
    public function testGetCommand()
    {
        $callable = function() {};
        $cmd = new Command('foo', $callable);
        $helperset = new HelperSet();
        $helperset->setCommand($cmd);
        $this->assertEquals($cmd, $helperset->getCommand(), '->getCommand() retrieves stored command');
    }

    /**
     * @covers \Danack\Console\Helper\HelperSet::getIterator
     */
    public function testIteration()
    {
        $helperset = new HelperSet();
        $helperset->set($this->getGenericMockHelper('fake_helper_01', $helperset));
        $helperset->set($this->getGenericMockHelper('fake_helper_02', $helperset));

        $helpers = array('fake_helper_01', 'fake_helper_02');
        $i = 0;

        foreach ($helperset as $helper) {
            $this->assertEquals($helpers[$i++], $helper->getName());
        }
    }

   /**
     * Create a generic mock for the helper interface. Optionally check for a call to setHelperSet with a specific
     * helperset instance.
     *
     * @param string    $name
     * @param HelperSet $helperset allows a mock to verify a particular helperset set is being added to the Helper
     */
    private function getGenericMockHelper($name, HelperSet $helperset = null)
    {
        $mock_helper = $this->getMock('\Danack\Console\Helper\HelperInterface');
        $mock_helper->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        if ($helperset) {
            $mock_helper->expects($this->any())
                ->method('setHelperSet')
                ->with($this->equalTo($helperset));
        }

        return $mock_helper;
    }
}
