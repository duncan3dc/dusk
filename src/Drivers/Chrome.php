<?php

namespace duncan3dc\Laravel\Drivers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverCapabilities;

class Chrome implements DriverInterface
{
    /**
     * Configuration options for this Chrome driver.
     *
     * @var array
     */
    private $config;

    /**
     * Default configuration options for this Chrome driver.
     *
     * @var array
     */
    private $defaultConfig = [
        'port' => 9515,
        'headless' => true
    ];

    /**
     * The Chromedriver process instance.
     *
     * @var \Symfony\Component\Process\Process
     */
    private $process;

    /**
     * @var WebDriverCapabilities $capabilities The capabilities in use.
     */
    private $capabilities;


    /**
     * Create a new instance and automatically start the driver.
     */
    public function __construct($config = [])
    {
        // Backwards compatibility for specifying $port as the only argument
        if (is_numeric($config)) {
            $config = [
                'port' => $config
            ];
        }

        $this->config = array_merge($this->defaultConfig, $config);

        $this->start();

        $capabilities = DesiredCapabilities::chrome();

        $chromeOptions = new ChromeOptions;
        if ($this->config['headless']) {
            $chromeOptions->addArguments(["--headless"]);
        }
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $chromeOptions);

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
        return RemoteWebDriver::create("http://localhost:{$this->config['port']}", $this->capabilities);
    }


    /**
     * Start the Chromedriver process.
     *
     * @return $this
     */
    public function start(): DriverInterface
    {
        if (!$this->process) {
            $this->process = (new ChromeProcess($this->config['port']))->toProcess();
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
