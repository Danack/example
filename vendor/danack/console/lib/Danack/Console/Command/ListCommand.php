<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Danack\Console\Command;

use Danack\Console\Helper\DescriptorHelper;
use Danack\Console\Input\InputArgument;
use Danack\Console\Input\InputOption;
use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;
use Danack\Console\Input\InputDefinition;

/**
 * ListCommand displays the list of all available commands for the application.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ListCommand extends AbstractCommand
{

    /**
     * @var InputInterface
     */
    private $input;
    
    /**
     * @var OutputInterface
     */
    private $output;
    
    function getCallable() {
        $callable = function () {
            if ($this->input->getOption('xml')) {
                $this->input->setOption('format', 'xml');
            }

            $helper = new DescriptorHelper();
            $helper->describe($this->output, $this->getApplication(), array(
                'format'    => $this->input->getOption('format'),
                'raw_text'  => $this->input->getOption('raw'),
                'namespace' => $this->input->getArgument('namespace'),
            ));
        };
        
        return $callable;
    }

    /**
     * Return an array of parameters that should be passed to the callable.
     * They should have the correct name indexes, the order does not matter
     * as Auryn will inject them correctly.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    function parseInput(InputInterface $input, OutputInterface $output) {
        $this->input = $input;
        $this->output = $output;
        
        return [];
    }


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('list')
            ->setDefinition($this->createDefinition())
            ->setDescription('Lists commands')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command lists all commands:

  <info>php %command.full_name%</info>

You can also display the commands for a specific namespace:

  <info>php %command.full_name% test</info>

You can also output the information in other formats by using the <comment>--format</comment> option:

  <info>php %command.full_name% --format=xml</info>

It's also possible to get raw list of commands (useful for embedding command runner):

  <info>php %command.full_name% --raw</info>
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeDefinition()
    {
        return $this->createDefinition();
    }

    /**
     * {@inheritdoc}
     */
    private function createDefinition()
    {
        return new InputDefinition(array(
            new InputArgument('namespace', InputArgument::OPTIONAL, 'The namespace name'),
            new InputOption('xml', null, InputOption::VALUE_NONE, 'To output list as XML'),
            new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw command list'),
            new InputOption('format', null, InputOption::VALUE_REQUIRED, 'To output list in other formats', 'txt'),
        ));
    }
}
