<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Danack\Console\Tests\Fixtures;

use Danack\Console\Command\AbstractCommand;
use Danack\Console\Input\InputArgument;
use Danack\Console\Input\InputOption;
use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;

class DescriptorCommand2 extends AbstractCommand
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
            ->setName('descriptor:command2')
            ->setDescription('command 2 description')
            ->setHelp('command 2 help')
            ->addArgument('argument_name', InputArgument::REQUIRED)
            ->addOption('option_name', 'o', InputOption::VALUE_NONE)
        ;
    }
}
