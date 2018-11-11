<?php


namespace Danack\Console\Command;

use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;


interface Dispatchable {

    function getCallable();

    /**
     * Return an array of parameters that should be passed to the callable.
     * They should have the correct name indexes, the order does not matter 
     * as Auryn will inject them correctly.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    function parseInput(InputInterface $input, OutputInterface $output);
} 