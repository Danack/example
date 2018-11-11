<?php

use Danack\Console\Command\AbstractCommand;
use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;

class FoobarCommand extends AbstractCommand
{
    public $input;
    public $output;


    function parseInput(InputInterface $input, OutputInterface $output) {
        return [];
    }

    function getCallable() {
        return null;
    }

    protected function configure()
    {
        $this
            ->setName('foobar:foo')
            ->setDescription('The foobar:foo command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }
}
