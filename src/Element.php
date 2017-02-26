<?php

namespace duncan3dc\Laravel;

use Facebook\WebDriver\Remote\RemoteWebElement;
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
}
