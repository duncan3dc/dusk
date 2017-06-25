---
layout: default
title: Base URL
permalink: /usage/base-url/
---

The upstream library provides very basic base URL functionality via a static variable, this library follows [RFC 3986](https://tools.ietf.org/html/rfc3986#section-5.2) a little closer.

Here are a few examples to demonstrate how it works:

~~~php
$browser->setBaseUrl("http://example.com");

$browser->visit("/stuff"); # http://example.com/stuff
~~~

~~~php
$browser->setBaseUrl("http://example.com/account");

$browser->visit("section"); # http://example.com/account/section
$browser->visit("/section"); # http://example.com/section
~~~
