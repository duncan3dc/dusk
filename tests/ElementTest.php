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
}
