<?php

use Danack\Console\Command\AbstractCommand;
use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;




class BarBucCommand extends AbstractCommand
{

    function parseInput(InputInterface $input, OutputInterface $output) {
        return [];
    }

    function getCallable() {
        return null;
    }
    
    protected function configure()
    {
        $this->setName('bar:buc');
    }
}
