---
layout: default
title: Elements
permalink: /usage/elements/
api: Element
---

The upstream library returns [RemoteWebElement](https://facebook.github.io/php-webdriver/1.3.0/Facebook/WebDriver/Remote/RemoteWebElement.html) instances when dealing with elements.  
This means we lose the extra upstream functionality, so this library wraps elements in a custom class and ensures that methods returning a `RemoteWebElement` retain extra functionality.  

As an example, this isn't possible upstream:
~~~php
foreach ($browser->elements("table tr") as $tr) {
    echo $tr->getAttribute("title") . "\n";
    foreach ($tr->elements("td") as $td) {
        echo "\t" . $td->getAttribute("title") . "\n";
    }
}
~~~
Because the first call to `elements()` will return `RemoteWebElement` instances, that don't recognise the `elements()` method on line 3.


## Parents

The `Element` class also has a `parent()` method for accessing parent elements:

~~~html
<table class='one'>
  <tr class='list'>
    <td id='album'>Album</td>
    <td id='artist'>Artist</td>
  </tr>
</table>
~~~

~~~php
$td = $browser->element("#artist");
$tr = $td->parent(); # This will return the <tr> element
~~~

~~~php
$td = $browser->element("#artist");
$table = $td->parent(".one"); # This will return the <table> element
~~~
