<?php

use Danack\Console\Application;
use Danack\Console\Command\Command;
use Danack\Console\Input\InputArgument;

/**
 * @param Application $console
 */
function add_console_commands(Application $console)
{
    addDebugCommand($console);
}

/**
 * @param Application $console
 */
function addDebugCommand(Application $console)
{
    $command = new Command('debug:hello', 'Example\CliController\Debug::hello');
    $command->setDescription("Test cli commands are working.");
    $console->add($command);
}

