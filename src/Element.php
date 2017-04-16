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

    /**
     * @var ElementResolver $resolver The resolver to use.
     */
    private $resolver;


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


    /**
     * Get all of the elements matching the given selector.
     *
     * @param string $selector
     *
     * @return Element[]
     */
    public function elements($selector)
    {
        $elements = $this->resolver->all($selector);
        return array_map(["self", "convertElement"], $elements);
    }


    /**
     * Get the element matching the given selector.
     *
     * @param string $selector
     *
     * @return Element|null
     */
    public function element($selector)
    {
        $element = $this->resolver->find($selector);
        return self::convertElement($element);
    }


    /**
     * Get one of the parents of this element.
     *
     * @param string $selector
     *
     * @return Element|null
     */
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


    /**
     * Click the element at the given selector (or this element).
     *
     * @param string $selector
     *
     * @return $this
     */
    public function click($selector = null)
    {
        if ($selector === null) {
            $element = $this->driver;
        } else {
            $element = $this->resolver->findOrFail($selector);
        }

        $element->click();

        return $this;
    }
}
