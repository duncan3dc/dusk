<?php

namespace duncan3dc\Laravel;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Concerns\InteractsWithElements;
use Laravel\Dusk\ElementResolver;

class Element
{
    use InteractsWithElements;

    /**
     * @var RemoteWebElement $driver The element to wrap.
     */
    private $driver;


    public static function convertElement($element)
    {
        if ($element instanceof RemoteWebElement) {
            return new self($element);
        }

        return $element;
    }


    public function __construct(RemoteWebElement $element)
    {
        $this->driver = $element;
        $this->resolver = new ElementResolver($this->driver);
    }


    public function __call($function, $args)
    {
        $result = $this->driver->$function(...$args);

        $result = self::convertElement($result);

        if (is_array($result)) {
            $result = array_map(["self", "convertElement"], $result);
        }

        return $result;
    }


    public function parent($selector = "*")
    {
        if ($selector === "*") {
            $prefix = "parent";
        } else {
            $prefix = "ancestor";
        }

        $selector = preg_replace("/\.([a-zA-Z0-9_-]+)/", "[contains(@class, '$1')]", $selector);

        return $this->findElement(WebDriverBy::xpath("{$prefix}::{$selector}"));
    }
}
