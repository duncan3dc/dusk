<?php

namespace duncan3dc\Laravel\Drivers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverCapabilities;
use Laravel\Dusk\Chrome\SupportsChrome;

class Chrome implements DriverInterface
{
    use SupportsChrome;

    private static $afterClass;

    private $capabilities;


    /**
     * Create a new instance and automatically start the driver.
     */
    public function __construct()
    {
        static::startChromeDriver();
    }


    /**
     * {@inheritDoc}
     */
    public function setCapabilities(WebDriverCapabilities $capabilities)
    {
        $this->capabilities = $capabilities;
        return $this;
    }


    /**
     * {@inheritDoc}
     */
    public function getCapabilities(): WebDriverCapabilities
    {
        if ($this->capabilities === null) {
            $this->capabilities = DesiredCapabilities::chrome();

            $options = (new ChromeOptions)->addArguments(["--headless"]);
            $this->capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        }

        return $this->capabilities;
    }


    /**
     * {@inheritDoc}
     */
    public function getDriver()
    {
        $capabilities = $this->getCapabilities();

        return RemoteWebDriver::create("http://localhost:9515", $capabilities);
    }


    /**
     * Required for upstream compatibility.
     */
    protected static function afterClass($handler)
    {
        self::$afterClass = $handler;
    }


    /**
     * Ensure the driver is closed by the upstream library.
     */
    public function __destruct()
    {
        $handler = self::$afterClass;
        $handler();
    }
}
