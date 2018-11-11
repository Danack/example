Chrome Mink Driver
==================

Mink driver for controlling chrome without the overhead of selenium.

It communicates directly with chrome over HTTP and WebSockets, which allows it to work at least twice as fast as chrome with selenium.

For chrome 59+ it supports headless mode, eliminating the need to install a display server, and the overhead that comes with it.

This driver is tested and benchmarked against a behat suite of 1800 scenarios and 19000 steps. It can successfully run it in less than 18 minutes with chrome 60 headless.

The same suite running against chrome 58 with xvfb and selenium takes ~60 minutes.

## Installation:

```bash
composer require dmore/chrome-mink-driver
```

## Requirements:

* Google chrome or chromium running with remote debugging

Example:

```bash
google-chrome-stable --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222
```

or headless (59+):

```bash
google-chrome-unstable --disable-gpu --headless --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222
```

The official docker image includes chrome 60 running headless.

See https://gitlab.com/DMore/behat-chrome-skeleton for a fully working example.

## Usage:

```php
use Behat\Mink\Mink;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;

$mink = new Mink(array(
    'browser' => new Session(new ChromeDriver('http://localhost:9222', null, 'http://www.google.com'))
));

```

Since Chrome 62+ there is the experimental option to allow file downloads which can be triggered with options being
passed to the ChromeDriver

| Option           | Value                                        |
|------------------|----------------------------------------------|
| downloadBehavior | allow, default, deny                         |
| downloadPath     | e.g. /tmp/ (/tmp/ is the default             |

Usage:

```php
use Behat\Mink\Mink;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;

use Selenium\Client as SeleniumClient;

$mink = new Mink(array(
    'chrome' => new Session(new ChromeDriver('http://localhost:9222', null, 'http://www.google.com', ['downloadBehavior' => 'allow', 'downloadPath' => '/tmp/'])),
));

```

Besides the above mentioned download options the following options can be provided

| Option           | Value               | Description                               |
|------------------|---------------------|-------------------------------------------|
| socketTimeout    | int, default: 10    | Connection timeout in seconds             |
| domWaitTimeout   | int, default: 3000  | DOM ready waiting timeout in milliseconds |

## Rendering PDF and Screenshots

Despite the Mink functionality the driver supports printing PDF pages or capturing a screenshot.

Usage:

```php
use Behat\Mink\Mink;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;

$mink = new Mink(array(
    'browser' => new Session(new ChromeDriver('http://localhost:9222', null, 'http://www.google.com'))
));

// set the default session name
$mink->setDefaultSessionName('browser');

// visit a page
$mink->getSession()->visit('https://gitlab.com/DMore/chrome-mink-driver/blob/master/README.md');

/** @var ChromeDriver $driver */
$driver = $mink->getSession()->getDriver();

$driver->printToPdf('/tmp/readme.pdf');
```

The available options are documented here: https://chromedevtools.github.io/devtools-protocol/tot/Page/#method-printToPDF

You can capture a screenshot with the `captueScreenshot` method. Options are documented here.

Please note, it is recommended to start Chrome with the disable-extensions flag like:

```bash
google-chrome-stable --disable-gpu --headless --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222 --window-size="1920,1080" --disable-extensions
```

## Behat

See [the behat extension](https://gitlab.com/DMore/behat-chrome-extension) if you want to use this driver with behat.

## Contributing

You are encouraged to fork this repository and contribute your own improvements.

See [the contribution guide](CONTRIBUTING.md) for instructions.
