<?php

namespace duncan3dc\Laravel\Drivers;

use Symfony\Component\Process\Process;

class ChromeProcess extends \Laravel\Dusk\Chrome\ChromeProcess
{
    /**
     * The port to run the Chromedriver on.
     *
     * @var int
     */
    private $port;

    /**
     * Create a new instance.
     *
     * @param int $port The port to run on
     */
    public function __construct(int $port = null)
    {
        parent::__construct();
        $this->port = $port ?: 9515;
    }


    /**
     * Build the Chromedriver with Symfony Process.
     *
     * @return Process
     */
    protected function process()
    {
        return (new Process(
            [realpath($this->driver), " --port={$this->port}"], null, $this->chromeEnvironment()
        ));
    }
}
