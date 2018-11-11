TODO list
---------

There are a few small issues with the library that should be addressed at some point. These are not new issues, but rather issues that existed in the Symfony/Console library, and can be fixed as the library no longer need to wait for a major Symfony revision to change BC issues:


* `Parse error should be a specific exception so it can be caught <https://github.com/Danack/Console/issues/4>`_

Currently the code throws a RuntimeException when the arguments don't meet the requirements of a command.

There should be an exception specific to this library that is thrown, so that people can catch parse exceptions separate from RuntimeExceptions.


* `Validator using exception for flow control <https://github.com/Danack/Console/issues/5>`_

Currently the validation for Questions need to throw an exception to indicate that the input did not pass the validation. Using exception for program flow control in anything other than exceptional circumstances is bad.


* `By default, lots of items are set as options for every command <https://github.com/Danack/Console/issues/3>`_

As well as the asked for arguments and options, currently the generic Command object will return other items like 'quiet', 'verbose' etc. These should probably be accessed through a different function. They weren't explicitly asked for, so they shouldn't be returned by default.

