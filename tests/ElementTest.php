<?php

namespace duncan3dc\LaravelTests;

use duncan3dc\Laravel\Element;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Mockery;

class ElementTest extends \PHPUnit_Framework_TestCase
{
    private $remote;
    private $element;


    public function setUp()
    {
        $this->remote = Mockery::mock(RemoteWebElement::class);
        $this->element = new Element($this->remote);
    }


    public function tearDown()
    {
        Mockery::close();
    }


    public function elementProvider()
    {
        yield [Mockery::mock(RemoteWebElement::class), true];

        $inputs = [
            $this->element,
            new Element(Mockery::mock(RemoteWebElement::class)),
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
     */
    public function testConvertElement($input, $wrap)
    {
        $result = Element::convertElement($input);

        if ($wrap) {
            $this->assertInstanceOf(Element::class, $result);
        } else {
            $this->assertSame($input, $result);
        }
    }


    public function testProxy()
    {
        $this->remote->shouldReceive("passthru")->with("one", "two")->andReturn("yep");

        $result = $this->element->passthru("one", "two");
        $this->assertSame("yep", $result);
    }


    public function testProxyElement()
    {
        $this->remote->shouldReceive("passthru")->with("#main")->andReturn(Mockery::mock(RemoteWebElement::class));

        $result = $this->element->passthru("#main");
        $this->assertInstanceOf(Element::class, $result);
    }


    public function testProxyElements()
    {
        $this->remote->shouldReceive("passthru")->with(".page")->andReturn([
            Mockery::mock(RemoteWebElement::class),
            Mockery::mock(RemoteWebElement::class),
            Mockery::mock(RemoteWebElement::class),
        ]);

        $result = $this->element->passthru(".page");
        $this->assertContainsOnlyInstancesOf(Element::class, $result);
    }


    public function parentProvider()
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
     */
    public function testParent($selector, $xpath)
    {
        $this->remote->shouldReceive("findElement")->with(\Mockery::on(function ($param) use ($xpath) {
            if ($param->getMechanism() !== "xpath") {
                return false;
            }
            return ($param->getValue() === $xpath);
        }))->andReturn("parent");

        $result = $this->element->parent($selector);
        $this->assertSame("parent", $result);
    }
}
