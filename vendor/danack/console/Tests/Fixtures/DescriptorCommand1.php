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
use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;


class DescriptorCommand1 extends AbstractCommand
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
            ->setName('descriptor:command1')
            ->setAliases(array('alias1', 'alias2'))
            ->setDescription('command 1 description')
            ->setHelp('command 1 help')
        ;
    }
}
