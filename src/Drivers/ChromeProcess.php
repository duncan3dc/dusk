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
     * @param array<string> $arguments
     *
     * @return Process<int, string>
     */
    public function toProcess(array $arguments = []): Process
    {
        $arguments[] = "--port={$this->port}";

        return parent::toProcess($arguments);
    }
}
