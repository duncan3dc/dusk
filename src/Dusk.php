<?php

namespace duncan3dc\Laravel;

use duncan3dc\Laravel\Drivers\Chrome;
use duncan3dc\Laravel\Drivers\DriverInterface;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Laravel\Dusk\Browser;

class Dusk
{
    /**
     * @var Browser $browser The browser instance to use.
     */
    private $browser;


    /**
     * @var string $baseUrl The base url.
     */
    private $baseUrl;


    /**
     * Create a new instance.
     *
     * @param DriverInterface $driver The browser driver to use
     */
    public function __construct(DriverInterface $driver = null)
    {
        if ($driver === null) {
            $driver =  new Chrome;
        }

        $this->browser = new Browser($driver->getDriver());
    }


    /**
     * Set the base url to use.
     *
     * @param string $url The base url
     *
     * @return $this
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = rtrim($url, "/");

        return $this;
    }


    /**
     * Proxy any methods to the internal browser instance.
     *
     * @param string $function The method name to call
     * @param array $args The parameters to pass to the method
     *
     * @return mixed
     */
    public function __call($function, $args)
    {
        $result = $this->browser->$function(...$args);

        if ($result instanceof Browser) {
            return $this;
        }

        $result = Element::convertElement($result);

        if (is_array($result)) {
            $result = array_map([Element::class, "convertElement"], $result);
        }

        return $result;
    }


    /**
     * Get the internal Browser instance in use.
     *
     * @return Browser
     */
    public function getBrowser()
    {
        return $this->browser;
    }


    /**
     * Get the internal web driver instance in use.
     *
     * @return RemoteWebDriver
     */
    public function getDriver()
    {
        return $this->browser->driver;
    }


    /**
     * Browse to the given URL.
     *
     * @param string $url The URL to visit
     *
     * @return $this
     */
    public function visit($url)
    {
        $url = $this->applyBaseUrl($url);

        $this->getBrowser()->visit($url);

        return $this;
    }


    /**
     * Ensure the passed url uses the current base.
     *
     * @param string $url The URL to convert
     *
     * @return string
     */
    private function applyBaseUrl($url)
    {
        if ($this->baseUrl === null) {
            return $url;
        }

        if (substr($url, 0, 7) === "http://") {
            return $url;
        }

        if (substr($url, 0, 8) === "https://") {
            return $url;
        }

        $baseUrl = $this->baseUrl;

        if (substr($url, 0, 1) === "/") {
            $path = parse_url($baseUrl, \PHP_URL_PATH);
            if ($path !== null) {
                $baseUrl = substr($baseUrl, 0, strlen($path) * -1);
            }
        }

        return "{$baseUrl}/" . ltrim($url, "/");
    }


    /**
     * Take a screenshot and store it on disk.
     *
     * @param string $filename The filename to store (no extension)
     *
     * @return $this
     */
    public function screenshot($filename)
    {
        $this->getDriver()->takeScreenshot("/tmp/{$filename}.png");

        return $this;
    }


    /**
     * Ensure the browser is closed down after use.
     */
    public function __destruct()
    {
        $this->browser->quit();
        unset($this->browser);
    }
}
