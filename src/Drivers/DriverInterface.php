<?php

namespace duncan3dc\Laravel\Drivers;

use Facebook\WebDriver\Remote\RemoteWebDriver;

interface DriverInterface
{

    /**
     * Get the web driver instance for this browser.
     *
     * @return RemoteWebDriver
     */
    public function getDriver();
}
