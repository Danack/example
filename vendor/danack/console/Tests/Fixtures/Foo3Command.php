<?php

use Danack\Console\Command\AbstractCommand;
use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;

class Foo3Command extends AbstractCommand
{

    function parseInput(InputInterface $input, OutputInterface $output) {
        return [];
    }

    function getCallable() {
        return null;
    }
    
    protected function configure()
    {
        $this
            ->setName('foo3:bar')
            ->setDescription('The foo3:bar command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            try {
                throw new \Exception("First exception <p>this is html</p>");
            } catch (\Exception $e) {
                throw new \Exception("Second exception <comment>comment</comment>", 0, $e);
            }
        } catch (\Exception $e) {
            throw new \Exception("Third exception <fg=blue;bg=red>comment</>", 0, $e);
        }
    }
}
