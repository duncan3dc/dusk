<?php

namespace duncan3dc\Laravel\Drivers;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\Chrome\SupportsChrome;

class Chrome implements DriverInterface
{
    use SupportsChrome;

    private static $afterClass;


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
    public function getDriver()
    {
        return RemoteWebDriver::create("http://localhost:9515", DesiredCapabilities::chrome());
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
