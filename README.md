# dusk
Use Dusk browser automation as a standalone component without the full Laravel framework

Full documentation is available at http://duncan3dc.github.io/dusk/  
PHPDoc API documentation is also available at [http://duncan3dc.github.io/dusk/api/](http://duncan3dc.github.io/dusk/api/namespaces/duncan3dc.Laravel.html)  

[![release](https://poser.pugx.org/duncan3dc/dusk/version.svg)](https://packagist.org/packages/duncan3dc/dusk)
[![build](https://github.com/duncan3dc/dusk/workflows/.github/workflows/buildcheck.yml/badge.svg?branch=master)](https://github.com/duncan3dc/dusk/actions?query=branch%3Amaster+workflow%3A.github%2Fworkflows%2Fbuildcheck.yml)
[![coverage](https://codecov.io/gh/duncan3dc/dusk/graph/badge.svg)](https://codecov.io/gh/duncan3dc/dusk)

## Installation

The recommended method of installing this library is via [Composer](//getcomposer.org/).

Run the following command from your project root:

```bash
$ composer require duncan3dc/dusk
```


## Getting Started

```php
use duncan3dc\Laravel\Dusk;

require __DIR__ . "/vendor/autoload.php";

$dusk = new Dusk;

$dusk->visit("http://example.com");
echo $dusk->element("h1")->getText() . "\n";
```

_Read more at http://duncan3dc.github.io/dusk/_  


## Changelog
A [Changelog](CHANGELOG.md) has been available since the beginning of time


## Where to get help
Found a bug? Got a question? Just not sure how something works?  
Please [create an issue](//github.com/duncan3dc/dusk/issues) and I'll do my best to help out.  
Alternatively you can catch me on [Twitter](https://twitter.com/duncan3dc)
