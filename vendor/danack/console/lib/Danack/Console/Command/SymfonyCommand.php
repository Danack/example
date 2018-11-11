<?php


namespace lib\Danack\Console\Command;

namespace Danack\Console\Command;
use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;

/**
 * Class SymfonyCommand Helper class that allows fast migrating from Symfony/Console
 * to Danack/Console. Simply change any commands that you don't want to re-write from 
 * extending Symfony\Component\Console\Command to extend \Danack\Console\Command\SymfonyCommand
 * and the parseInput() and getCallable() methods will be made available, and your current
 * execute() method will be called via the callable if this command needs to be dispatched.
 * 
 * @package Danack\Console\Command
 */
abstract class SymfonyCommand extends AbstractCommand {

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    protected abstract function execute(InputInterface $input, OutputInterface $output);

    /**
     * Do nothing on parse step - instead just hold the input and output
     * internally and only use them if requried. Holding state is bad but
     * yolo.
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array|mixed
     */
    function parseInput(InputInterface $input, OutputInterface $output) {
        $this->input = $input;
        $this->output = $output;

        return [];
    }

    /**
     * Return a callable that calls this classes execute method
     * @return callable
     */
    public function getCallable() {
        return function() {
            $this->execute($this->input, $this->output);
        };
    }
    
}

 