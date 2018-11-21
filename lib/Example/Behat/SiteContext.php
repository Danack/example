<?php

declare(strict_types=1);

namespace Example\Behat;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Mink\Exception\ResponseTextException;
use Behat\Mink\Element\NodeElement;

class SiteContext extends MinkContext
{
    /**
     * @Then /^I should see a list of books$/
     */
    public function iShouldSeeAListOfBooks()
    {
        $this->assertSession()->pageTextContains("Peopleware");
        $this->assertSession()->pageTextContains("Systemantics / The Systems Bible");
    }

    /**
     * Take screenshot when step fails.
     * Works only with Selenium2Driver.
     *
     * @AfterStep
     */
    public function takeScreenshotAfterFailedStep(AfterStepScope $event)
    {
        if (!$event->getTestResult()->isPassed()) {
            $this->takeScreenshot($event, $event->getFeature()->getTitle());
        }
    }

    private function takeScreenshot(AfterStepScope $event, $title)
    {
        $screenshot = $this->getSession()->getDriver()->getScreenshot();

        echo "Filename " . $event->getFeature()->getFile() . PHP_EOL;
        echo "Title " . $event->getFeature()->getTitle() . PHP_EOL;
        echo "Line " . $event->getStep()->getLine() . PHP_EOL;

        $filename = sprintf(
            __DIR__ . "/../../../test/screenshot/screenshot_%s_%d.png",
            $title,
            $event->getStep()->getLine()
        );

        @mkdir(dirname($filename), 0755, true);

        file_put_contents($filename, $screenshot);
    }

    public function getNumberOfIframes()
    {
        $session = $this->getSession();
        $page = $session->getPage();

        $iframeNodes = $page->findAll('css', 'iframe');

        return count($iframeNodes);
    }

    /**
     * @Given /^I wait to see "([^"]*)"$/
     */
    public function iWaitToSee($text)
    {
        $numberOfIframes = $this->getNumberOfIframes();
        for ($attempts=0; $attempts<10; $attempts++) {
            for ($iframe=0; $iframe< $numberOfIframes; $iframe++) {
                /** @var $iframe string */
                $this->getSession()->getDriver()->switchToIFrame($iframe);
                try {
                    $this->assertSession()->pageTextContains($this->fixStepArgument($text));
                    return true;
                } catch (ResponseTextException $rte) {
                    // not found
                }
            }
            sleep(1);
        }
        throw new \Exception("Failed to see [$text] on page.");

        // TODO - switch back.
        // $this->getSession()->getDriver()->switchToIFrame(0);
    }

//    /**
//     * @Then all caches are cleared
//     */
//    public function varnishCacheIsCleared()
//    {
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'XCGFULLBAN');
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_exec($ch);
//        curl_close($ch);
//
//        $this->getSession()->restart();
//    }
}
