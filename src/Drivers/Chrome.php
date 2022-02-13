<?php

namespace duncan3dc\Laravel\Drivers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverCapabilities;
use Symfony\Component\Process\Process;

class Chrome implements DriverInterface
{
    /** @var int */
    private $port;

    /** @var Process|null */
    private $process;

    /** @var WebDriverCapabilities */
    private $capabilities;


    /**
     * Create a new instance and automatically start the driver.
     */
    public function __construct(int $port = 9515)
    {
        $this->port = $port;

        $this->start();

        $capabilities = DesiredCapabilities::chrome();

        $options = (new ChromeOptions())->addArguments(["--headless"]);
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
        return RemoteWebDriver::create("http://localhost:{$this->port}", $this->capabilities);
    }


    /**
     * Start the Chromedriver process.
     *
     * @return $this
     */
    public function start(): DriverInterface
    {
        if (!$this->process) {
            $this->process = (new ChromeProcess($this->port))->toProcess();
            $this->process->start();
            sleep(1);
        }

        return $this;
    }


    /**
     * Ensure the driver is closed by the upstream library.
     *
     * @return $this
     */
    public function stop(): DriverInterface
    {
        if ($this->process) {
            $this->process->stop();
            unset($this->process);
        }

        return $this;
    }


    /**
     * Automatically end the driver when this class is done with.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->stop();
    }
}
