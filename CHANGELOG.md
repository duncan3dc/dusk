Changelog
=========

## x.y.z - UNRELEASED

--------

## 1.0.1 - 2019-05-24

### Changed

* [Laravel] Updated to work with version 5.1 from upstream.

--------

## 1.0.0 - 2019-03-25

### Changed

* [Laravel] Updated to work with version 5.0 from upstream.

--------

## 0.9.0 - 2018-10-29

### Changed

* [Support] Added support for PHP 7.3.
* [Laravel] Updated to work with version 4.0 from upstream.

--------

## 0.8.0 - 2018-05-08

### Added

* [Dusk] Allow `screenshot()` to be called with a fully qualified path.

--------

## 0.7.1 - 2018-04-07

### Fixed

* [Dependencies] Add a dependency on PHPUnit because upstream requires it but doesn't declare it.

--------

## 0.7.0 - 2018-04-05

### Added

* [Chrome] Allow the port to be specified.

### Changed

* [Support] Dropped support for PHP 7.0.
* [Laravel] Updated to work with version 3.0 from upstream.

--------

## 0.6.0 - 2018-02-05

### Changed

* [Drivers] Use Chrome in headless mode by default.

--------

## 0.5.0 - 2018-01-08

### Added

* [Drivers] Added a method to set the capabilities of the browser.

--------

## 0.4.1 - 2017-10-28

### Fixed

* [Dusk] Updated the code to handle a breaking change made upstream in 2.0.1.

--------

## 0.4.0 - 2017-07-05

### Added

* [Element] Allow the mouse to move over an element itself using Element::mouseover() with no selector.

--------

## 0.3.1 - 2017-07-04

### Fixed

* [Drivers] Ensure the drivers live as long as the Dusk instance (https://github.com/duncan3dc/dusk/issues/2).

--------

## 0.3.0 - 2017-06-25

### Added

* [Dusk] Add support for a base url.
* [Dusk] Allow the executeScript() method of the driver to be called.

--------

## 0.2.0 - 2017-04-30

### Added

* [Laravel] Compatibility with version 1.1 of laravel/dusk.
* [Element] Allow an element itself to be clicked on using Element::click() with no selector.

--------

## 0.1.1 - 2017-03-04

### Fixed

* [Element] Ensure Element::element() and Element::elements() return wrapper instance, and not RemoteWebElement instances.

--------

## 0.1.0 - 2017-02-26

### Added

* [Laravel] Compatibility with version 1.0 of laravel/dusk.
* [Drivers] A driver for Chrome.
* [Dusk] Add a screenshot() method.

--------
