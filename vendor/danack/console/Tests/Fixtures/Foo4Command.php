<?php

use Danack\Console\Command\AbstractCommand;
use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;


class Foo4Command extends AbstractCommand
{

    function parseInput(InputInterface $input, OutputInterface $output) {
        return [];
    }

    function getCallable() {
        return null;
    }
    
    protected function configure()
    {
        $this->setName('foo3:bar:toh');
    }
}
