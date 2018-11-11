
Console Router
==============

[![Build Status](https://travis-ci.org/Danack/Console.svg?branch=master)](https://travis-ci.org/Danack/Console)

This is not the Symfony console - it is a forking refactor to:

* To split the 'routing' and dispatching of commands.
* Remove the events, because they don't belong in what should be a reasonable, simple piece of code.
* Stop the console application catching and dumping exceptions when it has no idea how to handle them.

Basically although most of the Symfony/console library does a great job, the fact that you have to let it run the application is stupid. The console library should stick to console stuff, you should then be able to run the application yourself.

The example below shows how to create commands with a callable, have the console application 'route' the input, and then run the callable with [Auryn](https://github.com/rdlowrey/Auryn).


```php
$console = new Application();
$console->add(new AboutCommand());

// Create a command that will call the function 'uploadFile'
$uploadCommand = new Command('uploadFile', 'upload');
$uploadCommand->addArgument('name', InputArgument::REQUIRED, 'The name of the thing to foo');
$console->add($uploadCommand);

$helloWorldCallable = function ($name) {
    echo "Hello world, and particularly $name".PHP_EOL;
};

// Create a command that will call the closure
$callableCommand = new Command($helloWorldCallable, 'greet');
$callableCommand->addArgument('name', InputArgument::REQUIRED, 'The name of the person to say hello to.');
$console->add($callableCommand);

try {
    $parsedCommand = $console->parseCommandLine();
}
catch (\Exception $e) {
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


function uploadFile($filename) {
    echo "Need to upload the file $filename".PHP_EOL;
}

// Auryn needs scalars prefixed with a colon
function lowrey($params) {
    $newParams = [];
    foreach ($params as $key => $value) {
        $newParams[':'.$key] = $value;
    }
    return $newParams;
}
```

If the example above was in the file example.php running the command `php example.php greet Danack` would output:

> Hello world, and particularly Danack

\o/

If you want to see an example running please run the file Tests/example.php with some appropriate arguments e.g.:    

* php Tests/example.php upload backup.zip --dir=/var/log
* php Tests/example.php greet Danack
* php Tests/example.php greet

Will show the 'upload' and 'greet' commands being routed correctly

Migrating from Symfony/console
------------------------------

The only major work needed to migrate from Symfony/console to Danack/console is to change any command objects to return a callable instead of having an execute method.

This includes commands that just display information rather than having a 'proper' executable e.g. the [ListCommand](https://github.com/Danack/Console/blob/master/lib/Danack/Console/Command/ListCommand.php).

Then just change from:
 
* Application::run which runs the command and returns a status code
 
to
 
* Application::parseCommandLine which just parsed the command line args and returns a ParsedCommand object.




Previous readme
---------------


Console eases the creation of beautiful and testable command line interfaces.

Tests
-----

You can run the unit tests with the following command:

```bash
$ cd path/to/Symfony/Component/Console/
$ composer.phar install
$ phpunit
```

Third Party
-----------

`Resources/bin/hiddeninput.exe` is a third party binary provided within this
component. Find sources and license at https://github.com/Seldaek/hidden-input.

Resources
---------

[The Console Component](http://symfony.com/doc/current/components/console.html)

[How to create a Console Command](http://symfony.com/doc/current/cookbook/console/console_command.html)
