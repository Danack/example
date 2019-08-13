<?php

use Danack\Console\Application;
use Danack\Console\Command\Command;
use Danack\Console\Input\InputArgument;

/**
 * @param Application $console
 */
function add_console_commands(Application $console)
{
    addDebugCommands($console);
    addSetupCommands($console);
    addProcessCommands($console);



}

/**
 * @param Application $console
 */
function addDebugCommands(Application $console)
{
    $command = new Command('debug:hello', 'Example\CliController\Debug::hello');
    $command->setDescription("Test cli commands are working.");
    $console->add($command);

}

function addSetupCommands(Application $console)
{
    $command = new Command('setup:load_words', 'Example\CliController\Setup::loadWords');
    $command->setDescription("Loads the words from file in ./data/words.txt into the database.");
    $console->add($command);
}


/**
 * @param Application $console
 */
function addProcessCommands(Application $console)
{
    $command = new Command('process:alive_check', 'Example\CliController\AliveCheck::run');
    $command->setDescription("Place holder command to make sure commands are running .");
    $console->add($command);


    $command = new Command('process:invoice_pdf_generate', 'Example\CliController\PrintUrlToPdfQueueProcessor::run');
    $command->setDescription("Listens for InvoicePDF jobs and runs them .");
    $console->add($command);


    $command = new Command(
        'process:image_example',
        '\BackgroundWorkerExample\CliProcessor::processImageQueue'
    );
    $command->setDescription("Process the image queue.");
    $console->add($command);

}


