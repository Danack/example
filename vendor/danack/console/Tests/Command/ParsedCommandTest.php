<?php


use Danack\Console\Command\ParsedCommand;
use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;
use Danack\Console\Output\NullOutput;

use Danack\Console\Input\ArrayInput;

class ParsedCommandTest extends \PHPUnit_Framework_TestCase {

    
    function testNonCallable() {
        $input = new ArrayInput([]);
        $output = new NullOutput();
        
        $parsedCommand = new ParsedCommand(
            'Tests\Fixtures\NonStaticCallable::foo',
            [],
            $input,
            $output
        );
        //No assertion, just needs to not give an error
    }

}