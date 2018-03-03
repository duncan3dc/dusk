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

    /**
     * @var callable $afterClass A function to call after the class is finished with.
     */
    private static $afterClass;

    /**
     * @var WebDriverCapabilities $capabilities The capabilities in use.
     */
    private $capabilities;


    /**
     * Create a new instance and automatically start the driver.
     */
    public function __construct()
    {
        static::startChromeDriver();

        $capabilities = DesiredCapabilities::chrome();

        $options = (new ChromeOptions)->addArguments(["--headless"]);
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        $this->setCapabilities($capabilities);
    }


    /**
     * {@inheritDoc}
     */
    public function setCapabilities(WebDriverCapabilities $capabilities): void
    {
        $this->capabilities = $capabilities;
    }


    /**
     * {@inheritDoc}
     */
    public function getDriver(): RemoteWebDriver
    {
        return RemoteWebDriver::create("http://localhost:9515", $this->capabilities);
    }


    /**
     * Required for upstream compatibility.
     *
     * @param callable $handler A function to call after the class is finished with.
     *
     * @return void
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
