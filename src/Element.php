<?php

namespace duncan3dc\Laravel;

use Facebook\WebDriver\Interactions\Internal\WebDriverCoordinates;
use Facebook\WebDriver\Remote\FileDetector;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverPoint;
use Laravel\Dusk\Concerns\InteractsWithElements;
use Laravel\Dusk\ElementResolver;

/**
 * @method self clear()
 * @method self findElement(WebDriverBy $by)
 * @method self[] findElements(WebDriverBy $by)
 * @method ?string getAttribute(string $attribute_name)
 * @method string getCSSValue(string $css_property_name)
 * @method WebDriverPoint getLocation()
 * @method WebDriverPoint getLocationOnScreenOnceScrolledIntoView()
 * @method WebDriverCoordinates getCoordinates()
 * @method WebDriverDimension getSize()
 * @method string getTagName()
 * @method string getText()
 * @method bool isDisplayed()
 * @method bool isEnabled()
 * @method bool isSelected()
 * @method self sendKeys(mixed $value)
 * @method self setFileDetector(FileDetector $detector)
 * @method self submit()
 * @method string getID()
 * @method bool equals(WebDriverElement $other)
 */
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


    /**
     * Convert a standard element to one of our bespoke elements.
     *
     * @param mixed $element The element to convert
     * @param RemoteWebDriver $driver The driver that contains this element
     *
     * @return mixed
     */
    public static function convertElement($element, RemoteWebDriver $driver)
    {
        if ($element instanceof RemoteWebElement) {
            return new self($element, $driver);
        }

        return $element;
    }


    /**
     * Create a new instance.
     *
     * @param RemoteWebElement $element The element to wrap
     * @param RemoteWebDriver $driver The driver that contains this element
     */
    private function __construct(RemoteWebElement $element, RemoteWebDriver $driver)
    {
        $this->remote = $element;
        $this->resolver = new ElementResolver($this->remote);
        $this->driver = $driver;
    }


    /**
     * Pass a method call to the wrapped instance.
     *
     * @param string $method The name of the method to call
     * @param array $args The parameters to pass to the method
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $result = $this->remote->$method(...$args);

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
    public function elements(string $selector): array
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
     * @return ?Element
     */
    public function element(string $selector): ?Element
    {
        $element = $this->resolver->find($selector);
        return self::convertElement($element, $this->driver);
    }


    /**
     * Get one of the parents of this element.
     *
     * @param string $selector
     *
     * @return ?Element
     */
    public function parent(string $selector = "*"): ?Element
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
    public function click(string $selector = null): Element
    {
        if ($selector === null) {
            $element = $this->remote;
        } else {
            $element = $this->resolver->findOrFail($selector);
        }

        $element->click();

        return $this;
    }


    /**
     * Move the mouse over the given selector (or this element).
     *
     * @param string $selector
     *
     * @return $this
     */
    public function mouseover(string $selector = null): Element
    {
        if ($selector === null) {
            $element = $this->remote;
        } else {
            $element = $this->resolver->findOrFail($selector);
        }

        $this->driver->getMouse()->mouseMove($element->getCoordinates());

        return $this;
    }
}
