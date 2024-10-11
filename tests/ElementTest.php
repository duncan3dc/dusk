<?php

namespace duncan3dc\LaravelTests;

use duncan3dc\Laravel\Element;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class ElementTest extends TestCase
{
    /** @var RemoteWebElement|MockInterface  */
    private $remote;

    /** @var RemoteWebDriver|MockInterface  */
    private $driver;

    /** @var Element */
    private $element;


    public function setUp(): void
    {
        $this->remote = Mockery::mock(RemoteWebElement::class);
        $this->driver = Mockery::mock(RemoteWebDriver::class);

        $this->element = Element::convertElement($this->remote, $this->driver);
    }


    public function tearDown(): void
    {
        Mockery::close();
    }


    /**
     * @return iterable<array<mixed>>
     */
    public function elementProvider(): iterable
    {
        yield [Mockery::mock(RemoteWebElement::class), true];

        $inputs = [
            $this->element,
            Element::convertElement(Mockery::mock(RemoteWebElement::class), Mockery::mock(RemoteWebDriver::class)),
            false,
            true,
            (object) ["one" => 1],
        ];
        foreach ($inputs as $input) {
            yield [$input, false];
        }
    }


    /**
     * @dataProvider elementProvider
     *
     * @param mixed $input
     * @param bool $wrap
     */
    public function testConvertElement($input, bool $wrap): void
    {
        $result = Element::convertElement($input, $this->driver);

        if ($wrap) {
            $this->assertInstanceOf(Element::class, $result);
        } else {
            $this->assertSame($input, $result);
        }
    }


    public function testProxy(): void
    {
        $this->remote->shouldReceive("passthru")->with("one", "two")->andReturn("yep");

        $result = $this->element->passthru("one", "two");
        $this->assertSame("yep", $result);
    }


    public function testProxyElement(): void
    {
        $this->remote->shouldReceive("passthru")->with("#main")->andReturn(Mockery::mock(RemoteWebElement::class));

        $result = $this->element->passthru("#main");
        $this->assertInstanceOf(Element::class, $result);
    }


    public function testProxyElements(): void
    {
        $this->remote->shouldReceive("passthru")->with(".page")->andReturn([
            Mockery::mock(RemoteWebElement::class),
            Mockery::mock(RemoteWebElement::class),
            Mockery::mock(RemoteWebElement::class),
        ]);

        $result = $this->element->passthru(".page");
        $this->assertSame(3, count($result));
        $this->assertContainsOnlyInstancesOf(Element::class, $result);
    }


    /**
     * @return iterable<array<string>>
     */
    public function parentProvider(): iterable
    {
        $data = [
            "*"         =>  "parent::*",
            "div"       =>  "ancestor::div",
            "div.a"     =>  "ancestor::div[contains(@class, 'a')]",
            "div.a_b"   =>  "ancestor::div[contains(@class, 'a_b')]",
            "div.A-Z"   =>  "ancestor::div[contains(@class, 'A-Z')]",
        ];
        foreach ($data as $selector => $xpath) {
            yield [$selector, $xpath];
        }
    }


    /**
     * @dataProvider parentProvider
     *
     * @param string $selector
     * @param string $xpath
     *
     * @return void
     */
    public function testParent(string $selector, string $xpath): void
    {
        $parent = Mockery::mock(Element::class);

        $this->remote->shouldReceive("findElement")->with(\Mockery::on(function ($param) use ($xpath) {
            if ($param->getMechanism() !== "xpath") {
                return false;
            }
            return ($param->getValue() === $xpath);
        }))->andReturn($parent);

        $result = $this->element->parent($selector);
        $this->assertSame($parent, $result);
    }


    public function testElement(): void
    {
        $this->remote->shouldReceive("findElement")->andReturn(Mockery::mock(RemoteWebElement::class));

        $result = $this->element->element("#main");
        $this->assertInstanceOf(Element::class, $result);
    }


    public function testElements(): void
    {
        $this->remote->shouldReceive("findElements")->andReturn([
            Mockery::mock(RemoteWebElement::class),
            Mockery::mock(RemoteWebElement::class),
            Mockery::mock(RemoteWebElement::class),
        ]);

        $result = $this->element->elements(".page");
        $this->assertSame(3, count($result));
        $this->assertContainsOnlyInstancesOf(Element::class, $result);
    }


    public function testClick1(): void
    {
        $this->remote->shouldReceive("click");
        $result = $this->element->click();
        $this->assertInstanceOf(Element::class, $result);
    }
    public function testClick2(): void
    {
        $remote = Mockery::mock(RemoteWebElement::class);
        $remote->shouldReceive("click");
        $this->remote->shouldReceive("findElement")->andReturn($remote);

        $result = $this->element->click("a");
        $this->assertInstanceOf(Element::class, $result);
    }
}
