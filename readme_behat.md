
# Behat example

## Introduction

[Behat](http://behat.org/en/latest/) is a testing framework for testing 'business' expectations.

Behat tests are written using the [Gherkin language](https://en.wikipedia.org/wiki/Cucumber_(software)#Gherkin_language) and look like this:


```
Feature: Buying items

  Scenario: Too many items gives error message
    Given I have the maximum number of items in my basket
    When I try to add another item to my basket
    Then I see an error message for "too many items"
    And the number of items in my basket is still the maximum number of items.

```

The idea is that features can be defined in English (or at least business English) in a way that is understandable by both technical and non-technical staff.


## Technical parts

The Behat tests are run using the 'selenium-chrome' container. Behat runs in the PHP container (or locally to your machine) and sends messages to Selenium which controls the browser. 


## Running Behat tests

There is a script to run the Behat tests in the root of this project.

```
php vendor/bin/behat --config=./behat.yml --colors --stop-on-failure
```

While writing a new Behat test, or when debugging why one is failing, I recommend putting a tag on the test itself, like this:

Which allows you to run just the tests you are interested in with:

```
php vendor/bin/behat --config=./behat.yml --colors --stop-on-failure --tags @wip
```

rather than having to wait for all of the tests to be run.


## Screenshots of failed tests


For any test that fails, a screenshot will appear in the directory ./example/test/screenshot of the state of the browser when the test failed. This


## Observing Behat tests running

Sometimes it is difficult to figure out why tests are failing from just looking at error messages and a screenshot of the failed step.

To make it easier, you can watch the browser be controlled by Behat/Selenium as the tests are run.


* Download VNC Viewer from https://www.realvnc.com/en/connect/download/viewer/

* Open VNC Viewer and add a new connection at 'localhost:5900'. Click through the security warnings. 

Now when you run the Behat tests, the VNC Viewer will be showing you the browser running in the Selenium Chrome container.
