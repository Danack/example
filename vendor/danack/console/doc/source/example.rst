
Complete example
================

The example below puts everything together:
 
* Creating commands with a callable.
* Have the console application 'route' the input.
* Dispatch the callable with Auryn.

.. code-block:: php

    <?php

    $console = new Application();

    // Create a command that will call the function 'uploadFile'
    $uploadCommand = new Command('upload', 'uploadFile');
    $uploadCommand->addArgument('name', InputArgument::REQUIRED, 'The name of the thing to foo');
    $console->add($uploadCommand);

    $helloWorldCallable = function ($name) {
        echo "Hello world, and particularly $name".PHP_EOL;
    };
    
    // Create a command that will call the closure
    $callableCommand = new Command('greet', $helloWorldCallable);
    $callableCommand->addArgument('name', InputArgument::REQUIRED, 'The name of the person to say hello to.');
    $console->add($callableCommand);
    
    try {
        $parsedCommand = $console->parseCommandLine();
    }
    catch(\Exception $e) {
        // Exception was caught, either input was bad or parsing 
        // code failed dramatically.
        $output = new BufferedOutput();
        $console->renderException($e, $output);
        echo $output->fetch();
        exit(-1);
    }
    
    // We have a parsed command - execute its callable
    $provider = new Auryn\Provider();
    $provider->execute(
        $parsedCommand->getCallable(),
        formatKeyNames($parsedCommand->getParams())
    );

    function uploadFile($filename) {
        echo "Need to upload the file $filename".PHP_EOL;
    }

    // Auryn needs scalars prefixed with a colon
    function formatKeyNames($params) {
        $newParams = [];
        foreach ($params as $key => $value) {
            $newParams[':'.$key] = $value;
        }
        return $newParams;
    }


If the example above was in the file example.php running the command ``php example.php greet Danack`` would output:

``Hello world, and particularly Danack``


