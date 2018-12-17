<?php

declare(strict_types=1);

namespace Example\CliController;

use Behat\Mink\Mink;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;

class PdfGenerator
{
    /** @var string */
    private $chromeUri;

    /** @var string */
    private $tmpPath;

    public function __construct($chromeUri, $tmpPath)
    {
        $this->chromeUri = $chromeUri;
        $this->tmpPath = $tmpPath;
    }

    public function renderUrlAsPdf(string $url, string $filename)
    {
        $chromeDriver = new ChromeDriver($this->chromeUri, null, $url);

        $mink = new Mink([
            'browser' => new Session($chromeDriver)
        ]);

        // set the default session name
        $mink->setDefaultSessionName('browser');

        // visit a page
        $mink->getSession()->visit($url);
        if ($mink->getSession()->getStatusCode() !== 200) {
            throw new \Exception("Something went wrong generating pdf. Check [$url] is working");
        }

        /** @var ChromeDriver $driver */
        $driver = $mink->getSession()->getDriver();

        @mkdir(dirname($filename), 0755, true);
        $driver->printToPdf(
            $filename, // $filename,
            false, // $landscape
            false, // $displayHeaderFooter
            false, // $printBackground
            1, // $scale
            // A4 paper size:
            8.27, // $paperWidth
            11.69, // $paperHeight
            // Letter paper size:
            // 8.5, // $paperWidth
            // 11,  // $paperHeight
            0, // $marginTop
            0, // $marginBottom
            0, // $marginLeft
            0, // $marginRight
            '', // $pageRanges
            false,  // $ignoreInvalidPageRanges
            '',     // $headerTemplate
            ''      // $footerTemplate
        );
    }
}
