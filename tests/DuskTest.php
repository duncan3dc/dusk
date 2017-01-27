<?php

namespace duncan3dc\LaravelTests;

use duncan3dc\Laravel\Drivers\DriverInterface;
use duncan3dc\Laravel\Dusk;
use duncan3dc\ObjectIntruder\Intruder;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\Browser;
use Mockery;

class DuskTest extends \PHPUnit_Framework_TestCase
{
    private $dusk;

    public function setUp()
    {
        $driver = Mockery::mock(RemoteWebDriver::class);

        $factory = Mockery::mock(DriverInterface::class);
        $factory->shouldReceive("getDriver")->with()->andReturn($driver);

        $this->dusk = new Dusk($factory);

        $dusk = new Intruder($this->dusk);
        $dusk->browser = Mockery::mock(Browser::class);
        $dusk->browser->shouldReceive("quit");
        $dusk->browser->driver = $driver;
    }


    public function tearDown()
    {
        Mockery::close();
        unset($this->dusk);
    }


    public function testgetBrowser()
    {
        $this->assertInstanceOf(Browser::class, $this->dusk->getBrowser());
    }


    public function testGetDriver()
    {
        $this->assertInstanceOf(RemoteWebDriver::class, $this->dusk->getDriver());
    }


    public function testProxy()
    {
        $this->dusk->getBrowser()->shouldReceive("passthru")->with("one", "two")->andReturn("yep");

        $result = $this->dusk->passthru("one", "two");
        $this->assertSame("yep", $result);
    }
}
