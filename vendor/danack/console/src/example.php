<?php

/* Example file for using the console library as a pure CLI routing library.
 * The input below will give the listed output.
 * 
 * Input
 * =====
 * php Tests/example.php upload backup.zip --dir=/var/log
 * 
 * Output
 * ======
 * Need to upload the file backup.zip in the directory /var/log
 * 
 * Input
 * =====
 * php Tests/example.php greet Danack
 * 
 * Output
 * ======
 * Hello world, and particularly Danack
 *
 * Input 
 * =====
 * php Tests/example.php greet
 * 
 * Output
 * ======
 * Usage:
 * greet name
 * 
 */



use Danack\Console\Input\InputInterface;
use Danack\Console\Output\OutputInterface;
use Danack\Console\Application;
use Danack\Console\Command\AbstractCommand;
use Danack\Console\Command\Command;
use Danack\Console\Input\InputArgument;
use Danack\Console\Output\BufferedOutput;


require_once __DIR__."/../vendor/autoload.php";


/**
 * Class AboutCommand - An example command. Although you can write full Command objects
 * most of the time the GenericCommand object will be sufficient.
 */
class AboutCommand extends AbstractCommand {

    function parseInput(InputInterface $input, OutputInterface $output) {
        return [];
    }

    function getCallable() {
        return [$this, 'displayAbout'];
    }

    function displayAbout() {
        echo "This is an example application that shows a few simple commands being setup and executed by Auryn.";
    }

    protected function configure() {
        $this->setName('about')->
            setDescription('Short information about danack/console');
    }
}




function uploadFile($filename, $dir) {
    echo "Need to upload the file $filename in the directory $dir".PHP_EOL;
}

//Auryn needs scalars prefixed with a colon and we only
//have scalars here.
function lowrey($params) {
    $newParams = [];
    foreach ($params as $key => $value) {
        $newParams[':'.$key] = $value;
    }
    return $newParams;
}



$console = new Application();
$console->add(new AboutCommand());

$uploadCommand = new Command('upload', 'uploadFile');
$uploadCommand->addArgument('filename', InputArgument::REQUIRED, 'The name of the file to upload');
$uploadCommand->addOption('dir', null, InputArgument::OPTIONAL, 'Which directory to upload from', './');

$console->add($uploadCommand);


$helloWorldCallable = function ($name) {
    echo "Hello world, and particularly $name".PHP_EOL;
};

$callableCommand = new Command('greet', $helloWorldCallable);
$callableCommand->addArgument('name', InputArgument::REQUIRED, 'The name of the person to say hello to.');
$callableCommand->setDescription("Says hello to the world and one named person");
$console->add($callableCommand);

try {
    $parsedCommand = $console->parseCommandLine();
}
catch(\Exception $e) {
    $output = new BufferedOutput();
    $console->renderException($e, $output);
    echo $output->fetch();
    exit(-1);
}

$provider = new Auryn\Provider();
$provider->execute(
    $parsedCommand->getCallable(),
    lowrey($parsedCommand->getParams())
);






