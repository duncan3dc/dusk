---
layout: default
title: Drivers
permalink: /usage/drivers/
api: Drivers.DriverInterface
---

The `DriverInterface` is a very simple interface that can be injected into your `Dusk` instance to use a different driver.  

~~~php
$driver = new MyCustomDriverProvider;
$browser = new Dusk($driver);
~~~
