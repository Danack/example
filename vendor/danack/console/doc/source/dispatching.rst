Running the console
===================


Once the console application has been created and has had the commands attached to it,running the 'parseCommandLine' method on it will either
 
* return a ParsedCommand, that contains the requested callable and params.

or

* throw an exception if the command line arguments did not resolve to a valid Command. 


.. code-block:: php

    <?php
    
    $console = new Application();
    
    $console->add($uploadCommand);
    $console->add($downloadCommand);
    $console->add($statusCommand);
    $console->add(new URLDownloadCommand());

    try {
        $parsedCommand = $console->parseCommandLine();
    }
    catch(\Exception $e) {
        $output = new BufferedOutput();
        $console->renderException($e, $output);
        echo $output->fetch();
        exit(-1);
    }
    
    

You can use whichever library or code you like to dispatch the callables returned by the ParsedCommand object.

However I strongly recommend using `Auryn <https://github.com/rdlowrey/auryn>`_ - it's one of the only libraries to do Dependency Injection correctly, and is easier to use than the other DI libraries out there.  Dispatching callables with Auryn is trivial:

.. code-block:: php

    <?php

    $provider = new Auryn\Provider();
    
    // Pass in the callable, and the parameters
    $provider->execute(
        $parsedCommand->getCallable(),
        formatKeyNames($parsedCommand->getParams())
    );

    // Auryn needs scalars prefixed with a colon to separate them 
    // from class aliases
    function formatKeyNames($params) {
        $newParams = [];
        foreach ($params as $key => $value) {
            $newParams[':'.$key] = $value;
        }

        return $newParams;
    }

Supported callable types
------------------------

Auryn can run any of the following callable 'types':
 

* A closure i.e return function(){echo "foo";}; 
* [$objectInstance, 'methodName']
* 'globalFunctionName'
* 'MyStaticClass::myStaticMethod'
* ['MyStaticClass', 'myStaticMethod']
* ['MyChildStaticClass', 'parent::myStaticMethod']
* 'ClassNameThatHasMagicInvoke'
* $instanceOfClassThatHasMagicInvoke


And so if you're using Auryn you should return one of those types. If you're using a different library to invoke your callables YMMV.