Console
=======

This is not the Symfony console - it is a forking refactor to:

* Split the 'routing' and dispatching of commands.
* Remove the events, because they don't belong in what should be a reasonable, simple piece of code.
* Stop the console application catching and dumping exceptions when it has no idea how to handle them.


Although most of the Symfony/console library does a great job, the fact that you have to let it run the application is stupid. The console library should stick to console stuff, you should then be able to run the application yourself.

If you want to see an example running please download the library and run the file src/example.php with some appropriate arguments e.g.

* php src/example.php upload backup.zip --dir=/var/log
* php src/example.php greet Danack
* php src/example.php greet

Please refer to the 
`Symfony Console <http://symfony.com/doc/current/components/console/introduction.html>`_ documentation for the full set of feature that the Console component is capable of. This site merely documents how to use the fork as a pure 'CLI router' without having the console also dispatch commands.
