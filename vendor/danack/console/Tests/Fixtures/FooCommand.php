<?php

use Danack\Console\Command\AbstractCommand;
use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;


class FooCommand extends AbstractCommand
{
    public $input;
    public $output;


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
            ->setName('foo:bar')
            ->setDescription('The foo:bar command')
            ->setAliases(array('afoobar'))
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('interact called');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $output->writeln('called');
    }
}
