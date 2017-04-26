<?php

namespace duncan3dc\Laravel;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Concerns\InteractsWithElements;
use Laravel\Dusk\ElementResolver;

class Element
{
    use InteractsWithElements;

    /**
     * @var RemoteWebElement $remote The element to wrap.
     */
    private $remote;

    /**
     * @var ElementResolver $resolver The resolver to use.
     */
    private $resolver;

    /**
     * @var RemoteWebDriver $driver The parent driver instance.
     */
    private $driver;


    public static function convertElement($element, RemoteWebDriver $driver)
    {
        if ($element instanceof RemoteWebElement) {
            return new self($element, $driver);
        }

        return $element;
    }


    private function __construct(RemoteWebElement $element, RemoteWebDriver $driver)
    {
        $this->remote = $element;
        $this->resolver = new ElementResolver($this->remote);
        $this->driver = $driver;
    }


    public function __call($function, $args)
    {
        $result = $this->remote->$function(...$args);

        $result = self::convertElement($result, $this->driver);

        if (is_array($result)) {
            array_walk($result, function (&$element) {
                $element = self::convertElement($element, $this->driver);
            });
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

        array_walk($elements, function (&$element) {
            $element = self::convertElement($element, $this->driver);
        });

        return $elements;
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
        return self::convertElement($element, $this->driver);
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
            $element = $this->remote;
        } else {
            $element = $this->resolver->findOrFail($selector);
        }

        $element->click();

        return $this;
    }
}
