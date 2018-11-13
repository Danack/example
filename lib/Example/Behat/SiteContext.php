<?php

declare(strict_types=1);

namespace Example\Behat;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Hook\Scope\AfterStepScope;

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
}
