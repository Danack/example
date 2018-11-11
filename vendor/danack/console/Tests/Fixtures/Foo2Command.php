<?php

use Danack\Console\Command\AbstractCommand;
use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;

class Foo2Command extends AbstractCommand
{

    function parseInput(InputInterface $input, OutputInterface $output) {
        $this->input = $input;
        $this->output = $output;

        return [];
    }

    function getCallable() {
        $callable = function() {
            return $this->execute($this->input, $this->output);
        };

        return $callable;
    }
    
    protected function configure()
    {
        $this
            ->setName('foo1:bar')
            ->setDescription('The foo1:bar command')
            ->setAliases(array('afoobar2'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
