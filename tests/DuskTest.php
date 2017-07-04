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


    public function testDriverLives()
    {
        # Ensure no instances of the driver have been created
        $this->assertSame(0, Driver::$instances);

        # Create a new instance and ensure it's tracked
        $driver = new Driver;
        $this->assertSame(1, Driver::$instances);

        $dusk = new Dusk($driver);

        # Ensure that the driver still lives
        $this->assertSame(1, Driver::$instances);

        # Even after we stop using it here
        unset($driver);
        $this->assertSame(1, Driver::$instances);

        # Now we're done with dusk ensure the driver is destroyed
        unset($dusk);
        $this->assertSame(0, Driver::$instances);
    }


    public function testgetBrowser()
    {
        $this->assertInstanceOf(Browser::class, $this->dusk->getBrowser());
    }


    public function testGetDriver()
    {
        $this->assertInstanceOf(RemoteWebDriver::class, $this->dusk->getDriver());
    }


    public function testVisit()
    {
        $browser = $this->dusk->getBrowser();
        $browser->shouldReceive("visit")->with("http://example.com/")->andReturn($browser);

        $result = $this->dusk->visit("http://example.com/");
        $this->assertSame($this->dusk, $result);
    }


    public function baseUrlProvider()
    {
        $data = [
            "/sub/dir/"             =>  "http://example.com/sub/dir/",
            "/sub"                  =>  "http://example.com/sub",
            "sub/dir/"              =>  "http://example.com/base/url/sub/dir/",
            "sub"                   =>  "http://example.com/base/url/sub",
            "http://google.com"     =>  "http://google.com",
            "https://google.com"    =>  "https://google.com",
        ];
        foreach ($data as $input => $expected) {
            yield [$input, $expected];
        }
    }
    /**
     * @dataProvider baseUrlProvider
     */
    public function testSetBaseUrl($input, $expected)
    {
        $this->dusk->setBaseUrl("http://example.com/base/url");

        $browser = $this->dusk->getBrowser();
        $browser->shouldReceive("visit")->with($expected)->andReturn($browser);

        $result = $this->dusk->visit($input);
        $this->assertSame($this->dusk, $result);
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
        $browser->shouldReceive("resize")->with(800, 600)->andReturn($browser);

        $result = $this->dusk->resize(800, 600);
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


    public function testExecuteScript()
    {
        $this->dusk->getDriver()->shouldReceive("executeScript")->with("alert('ok')");

        $result = $this->dusk->executeScript("alert('ok')");
        $this->assertSame($this->dusk, $result);
    }
}
