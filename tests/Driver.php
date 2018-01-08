<?php

namespace duncan3dc\LaravelTests;

use duncan3dc\Laravel\Drivers\DriverInterface;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverCapabilities;
use Mockery;

class Driver implements DriverInterface
{
    public static $instances = 0;

    public function __construct()
    {
        ++self::$instances;
    }

    public function __destruct()
    {
        --self::$instances;
    }

    public function setCapabilities(WebDriverCapabilities $capabilities)
    {
    }

    public function getDriver()
    {
        $remote = Mockery::mock(RemoteWebDriver::class);
        $remote->shouldReceive("quit");
        return $remote;
    }
}
