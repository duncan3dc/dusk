---
layout: default
title: Setup
permalink: /setup/
---

All classes are in the `duncan3dc\Laravel` namespace.

~~~php
use duncan3dc\Laravel\Dusk;

require __DIR__ . "/vendor/autoload.php";

$dusk = new Dusk;
~~~

No setup is required, by default the Chrome driver will be used.  
Unless you want to use an alternative [Selenium driver](../usage/drivers).  
