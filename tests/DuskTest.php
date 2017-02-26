<?php

namespace duncan3dc\LaravelTests;

use duncan3dc\Laravel\Drivers\DriverInterface;
use duncan3dc\Laravel\Dusk;
use duncan3dc\Laravel\Element;
use duncan3dc\ObjectIntruder\Intruder;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
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


    public function testProxyBrowser()
    {
        $browser = $this->dusk->getBrowser();
        $browser->shouldReceive("visit")->with("http://example.com/")->andReturn($browser);

        $result = $this->dusk->visit("http://example.com/");
        $this->assertSame($this->dusk, $result);
    }


    public function testProxyElement()
    {
        $this->dusk->getBrowser()->shouldReceive("element")->with("#main")->andReturn(Mockery::mock(RemoteWebElement::class));

        $result = $this->dusk->element("#main");
        $this->assertInstanceOf(Element::class, $result);
    }


    public function testProxyElements()
    {
        $this->dusk->getBrowser()->shouldReceive("elements")->with(".page")->andReturn([
            Mockery::mock(RemoteWebElement::class),
            Mockery::mock(RemoteWebElement::class),
            Mockery::mock(RemoteWebElement::class),
        ]);

        $result = $this->dusk->elements(".page");
        $this->assertContainsOnlyInstancesOf(Element::class, $result);
    }


    public function testScreenshot()
    {
        $this->dusk->getDriver()->shouldReceive("takeScreenshot")->with("/tmp/page1.png");

        $result = $this->dusk->screenshot("page1");
        $this->assertSame($this->dusk, $result);
    }
}
