Creating Commands
=================


The simplest way to create commands is to just instantiate a Command object with:

* The name the of the command.
* The callable that needs to be called if that Command is the one matched by the command line.

And then attaching the arguments and options that need to be used by that command to it. All the arguments and options that are attached are automatically returned by the `parseInput` in the class.


.. code-block:: php

    <?php
    
    use Danack\Console\Command\Command;

    // Creates the command 'upload' that has the function 'uploadFile' 
    // as its callable
    $uploadCommand = new Command('upload', 'uploadFile');
    $uploadCommand->addArgument(
        'filename',
        InputArgument::REQUIRED,
        'The name of the file to upload'
    );
    
    // Creates the command 'download'. The callable for it is the method 'downloadFile' 
    // on the class 'Download'.
    $downloadCommand = new Command('download', ['Download', 'downloadFile']);
    $downloadCommand->addArgument(
        'filename',
        InputArgument::REQUIRED,
        'The name of the file to download'
    );

    
    //Create a status checker object.
    $statusChecker = new StatusChecker();
    // Creates the command 'download'. The callable for it is the method 'checkStatus' 
    // on the object '$statusChecker'.
    $statusCommand = new Command('checkStatus', [$statusChecker, 'checkStatus']);
    
    
You can also create your own Command objects by extending the class 'AbstractCommand'. This would be more appropriate when you wanted to filter the input e.g. to validate a URL.
    

.. code-block:: php

    <?php

    class URLDownloadCommand extends AbstractCommand {
    
        function parseInput(InputInterface $input, OutputInterface $output) {
            $url = $input->getArgument('url');
            $filteredUrl = filter_var($url, FILTER_VALIDATE_URL);
            
            if ($filteredUrl == false) {
                throw new \InputException("URL '$url' does not appear to be a valid URL.");
            }
        
            return ['url' => $filteredUrl];
        }
        
        function getCallable() {
            return [$this, 'displayAbout'];
        }
        
        function displayAbout($url) {
            // Download the URL.
        }
    
        protected function configure() {
            $this->
                setName('url')->
                setDescription('The url to download');
        }
    }


As in Symfony Console the commands need to be attached to the Console 'Application' object before the command line is parsed:
    
.. code-block:: php

    <?php

    $console = new Application();
    
    $console->add($uploadCommand);
    $console->add($downloadCommand);
    $console->add($statusCommand);
    $console->add(new URLDownloadCommand());
    
    
    
    