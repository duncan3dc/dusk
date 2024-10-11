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
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class DuskTest extends TestCase
{
    /** @var Dusk */
    private $dusk;

    /** @var Browser|MockInterface */
    private $browser;

    /** @var RemoteWebDriver|MockInterface */
    private $driver;


    /** @inheritDoc */
    public function setUp(): void
    {
        $this->driver = Mockery::mock(RemoteWebDriver::class);

        $factory = Mockery::mock(DriverInterface::class);
        $factory->shouldReceive("getDriver")->with()->andReturn($this->driver);

        $this->dusk = new Dusk($factory);

        $this->browser = Mockery::mock(Browser::class);
        $this->browser->shouldReceive("quit");

        $dusk = new Intruder($this->dusk);
        $dusk->browser = $this->browser;
        $dusk->browser->driver = $this->driver;
    }


    /** @inheritDoc */
    public function tearDown(): void
    {
        Mockery::close();
        unset($this->dusk);
    }


    public function testDriverLives(): void
    {
        # Ensure no instances of the driver have been created
        $this->assertSame(0, Driver::$instances);

        # Create a new instance and ensure it's tracked
        $driver = new Driver();
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


    public function testGetBrowser(): void
    {
        $this->assertInstanceOf(Browser::class, $this->dusk->getBrowser());
    }


    public function testGetDriver(): void
    {
        $this->assertInstanceOf(RemoteWebDriver::class, $this->dusk->getDriver());
    }


    public function testVisit(): void
    {
        $this->browser->shouldReceive("visit")->with("http://example.com/")->andReturn($this->browser);

        $result = $this->dusk->visit("http://example.com/");
        $this->assertSame($this->dusk, $result);
    }


    /**
     * @return iterable<array<string>>
     */
    public function baseUrlProvider(): iterable
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
     *
     * @param string $input
     * @param string $expected
     *
     * @return void
     */
    public function testSetBaseUrl(string $input, string $expected): void
    {
        $this->dusk->setBaseUrl("http://example.com/base/url");

        $this->browser->shouldReceive("visit")->with($expected)->andReturn($this->browser);

        $result = $this->dusk->visit($input);
        $this->assertSame($this->dusk, $result);
    }


    public function testProxy(): void
    {
        $this->browser->shouldReceive("passthru")->with("one", "two")->andReturn("yep");

        $result = $this->dusk->passthru("one", "two");
        $this->assertSame("yep", $result);
    }


    public function testProxyBrowser(): void
    {
        $this->browser->shouldReceive("resize")->with(800, 600)->andReturn($this->browser);

        $result = $this->dusk->resize(800, 600);
        $this->assertSame($this->dusk, $result);
    }


    public function testProxyElement(): void
    {
        $this->browser->shouldReceive("element")->with("#main")->andReturn(Mockery::mock(RemoteWebElement::class));

        $result = $this->dusk->element("#main");
        $this->assertInstanceOf(Element::class, $result);
    }


    public function testProxyElements(): void
    {
        $this->browser->shouldReceive("elements")->with(".page")->andReturn([
            Mockery::mock(RemoteWebElement::class),
            Mockery::mock(RemoteWebElement::class),
            Mockery::mock(RemoteWebElement::class),
        ]);

        $result = $this->dusk->elements(".page");
        $this->assertContainsOnlyInstancesOf(Element::class, $result);
    }


    public function testScreenshot1(): void
    {
        $this->driver->shouldReceive("takeScreenshot")->with("/tmp/page1.png");

        $result = $this->dusk->screenshot("page1");
        $this->assertSame($this->dusk, $result);
    }
    public function testScreenshot2(): void
    {
        $this->driver->shouldReceive("takeScreenshot")->with("/custom/path/page2.png");

        $result = $this->dusk->screenshot("/custom/path/page2");
        $this->assertSame($this->dusk, $result);
    }
    public function testScreenshot3(): void
    {
        $this->driver->shouldReceive("takeScreenshot")->with("/tmp/page3.png");

        $result = $this->dusk->screenshot("page3.png");
        $this->assertSame($this->dusk, $result);
    }


    public function testExecuteScript(): void
    {
        $this->driver->shouldReceive("executeScript")->with("alert('ok')");

        $result = $this->dusk->executeScript("alert('ok')");
        $this->assertSame($this->dusk, $result);
    }
}
