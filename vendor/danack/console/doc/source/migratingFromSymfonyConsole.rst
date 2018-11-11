.. toctree::
    :maxdepth: 1

Migrating from Symfony/Console
==============================

Hopefully one day the Symfony team will refactor Symfony/console to be usable as just a 'CLI router' but in the meantime, there aren't many changes required to move from Symfony/Console to Danack/Console.


Change the console application call
-----------------------------------

Change ``$application->run()`` which runs the command and returns a status code to ``$application->parseCommandLine()`` which just parses the command line args and returns a ParsedCommand object.


Change commands to implement the Dispatchable interface
-------------------------------------------------------

Commands need to implement the 'Dispatchable' interface.

* getCallable() - return a callable that should be executed if this command was requested.

* parseInput(InputInterface $input, OutputInterface $output) - return an array of all the parsed parameters that this command received. 


Feeling lazy?
-------------

You don't actually have to refactor all the commands by stripping out the executable - you can just
change your Command objects to extend ``Danack\Console\Command\SymfonyCommand`` which has the two methods required for the Dispatchable interface.
 
 
* The getCallable() method returns a closure to the Command's current execute method.

* The parseInput() method  grabs a reference to the $input and $output objects when the command is parsed, so that they can be used later by the execute method.


Obviously, I recommend refactoring your code to have your commands and the services be completely separate, and also not storing state, but not having to refactor lots of commands at once is quite nice.