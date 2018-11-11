<?php


namespace Danack\Console\Command;

use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;

/**
 * Class ParsedCommand Holds the information about what should be called, with what params
 * as a result of parsing the command line options in 'Application'.
 * @package Danack\Console\Command
 */
class ParsedCommand {

    /**
     * @var callable
     */
    private $callable;
    /**
     * @var array
     */
    private $params;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param null $callable What should be called to execute this command. Not
     * typehinted to callable, to allow custom schemas
     * @param array $params
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    function __construct($callable = null, array $params = null, InputInterface $input, OutputInterface $output) {
        $this->callable = $callable;
        $this->input = $input;
        $this->output = $output;
        $this->params = $params;
    }

    /**
     * @return callable
     */
    public function getCallable() {
        return $this->callable;
    }
    
    /**
     * @return InputInterface
     */
    public function getInput() {
        return $this->input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput() {
        return $this->output;
    }

    /**
     * @return array
     */
    public function getParams() {
        return $this->params;
    }
}

 