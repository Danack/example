Changelog
=========

## 2.6

* Add click on radio element when selected - [Peter Rehm](https://gitlab.com/peterrehm)
* Added support to capture screenshots and to render PDFs - [Peter Rehm](https://gitlab.com/peterrehm)
* Socket timeout defaults now to 10 seconds - [Matthew Hotchen](https://gitlab.com/mhotchen)
* Set pierce argument to TRUE if currently in an iframe  - [Sascha Grossenbacher](https://gitlab.com/saschagros)
* Added verification if an element can be focused  - [Sascha Grossenbacher](https://gitlab.com/saschagros)
* Fixed password fields not being focused before key presses

## 2.5

* Added option for overriding socket timeout - [Matthew Hotchen](https://gitlab.com/mhotchen)

## 2.4.3

* Fixed compatibility with Chrome 61 - [Matthew Hotchen](https://gitlab.com/mhotchen)

* PHP 7.2 compatibility - [Peter Rehm](https://gitlab.com/peterrehm)

## 2.4.2

* Removed dependency on symfony/options-resolver:3 due to conflicts with Symfony2 projects

## 2.4.1

* Added support for enabling certificate override [Arturas Smorgun](https://gitlab.com/asarturas)

* Fixed numeric passwords being treated as integers

## 2.4.0

* Fixed support for Chrome 62 - [Peter Rehm](https://gitlab.com/peterrehm)

* Implemented download behavior (Chrome 62+ only)  - [Peter Rehm](https://gitlab.com/peterrehm)

## 2.3.1

* Fixed 'Server sent invalid upgrade response' when switching windows, in some cases

## 2.3.0

* Fixed getWindowNames incompatibility with Selenium2Driver

* Fixed mouseover sometimes moving the mouse outside the element - [Mark Nielsen](https://gitlab.com/polothy)

* Fixed inability to switchToWindow for some tabs which were opened with window.open() -  [Mark Nielsen](https://gitlab.com/polothy)

* Throw DriverException instead of \Exception - [Mark Nielsen](https://gitlab.com/polothy)

* Throw NoSuchFrameException instead of generic \Exception when the frame is removed after being switched to - [Mark Nielsen](https://gitlab.com/polothy)

* Fixed clicking on an option tag which is inside an optgroup - [Mark Nielsen](https://gitlab.com/polothy)

* Fixed isVisible when the element is hidden using negative offsets or 'visibility: hidden' - [Mark Nielsen](https://gitlab.com/polothy)

* Fixed NoSuchElement exception when textbox is removed by javascript onchange - [Mark Nielsen](https://gitlab.com/polothy)

* Fixed browser resizing - [Mark Nielsen](https://gitlab.com/polothy)

* Added support for setting the value of a input type="search" - [Raphaël Droz](https://gitlab.com/drzraf)

* Added support for setting the value of an element with contenteditable=true - [Mark Nielsen](https://gitlab.com/polothy)

## 2.2.0

* Implemented isolation between multiple instances running against the same browser, if the browser is running with --headless

* Fixed isVisible when an element only has children which are floating, fixed, or absolute

* Fixed setValue on fields with limited length

* Fixed getStatusCode and getResponseHeaders timing out when the page has been loaded before the websocket was opened

* Fixed setValue for multibyte unicode

* Fixed some elements not receiving click

* Sped up animations and added sleep until they complete

* Fixed timeout when page loading takes longer than 5 seconds

* Fixed deadlock when a request fails

* Fixed deadlock when chrome crashes

* Fixed fields not showing autocomplete on setValue, due to unnecessary blur

* Fixed fatal error when restarting without --headless

## 2.1.1

* Fixed compatibility with 5.6 and 7.0

## 2.1.0

* Added support for switching to popups which chrome turned opened as tabs

* Improved findElementXpaths to get the shortest xpath possible

* Fixed xpath queries not always returning the elements in the correct order

* Fixed setValue not always triggering keyup/keydown

* Fixed popup blocker stopping popups triggered by click

* Fixed deadlock when javascript prompt/alert is shown

* Fixed double click not dispatching an event for the first click

* Fixed double click not bubbling

* Fixed page load timing out after 5 seconds

## 2.0.1

* Removed behat dependency

## 2.0.0

* Fixed screenshot feature (thanks https://gitlab.com/OmarMoper)

* Extracted behat extension to its own repository

## 1.1.3

* Fixed timeout when checking for the status code of a request served from cache

## 1.1.2

* PHP 5.6 support

* Fixed websocket timeout when visit() was not the first action after start() or reset()

## 1.1.1

* Licensed as MIT

## 1.1.0

* Added support for basic http authentication

* Added support for removing http-only cookies

* Added support for file upload

* Fixed getContent() returning htmldecoded text instead of the outer html as is

## 1.0.1

* Fixed back() and forward() timing out when the page is served from cache.
