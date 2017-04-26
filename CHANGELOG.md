Changelog
=========

## x.y.z - UNRELEASED

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
