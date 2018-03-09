<?php

namespace Evenflow\Workers;

use Evenflow\Workers\IWorker;

abstract class AbstractWorker implements IWorker
{
    protected $logger;
    
    abstract public function getTitle() : string;
    abstract public function execute(IWorkloadInput $workload) : IWorkloadOutput;

    /**
     * Sets the Logger instance used by the image processor.
     *
     * @param \Monolog\Logger $logger The Logger.
     *
     * @return void
     */
    public function setLogger(\Monolog\Logger $logger) : void
    {
        $this->logger = $logger->withName($this->getTitle());
    }
    
    /**
     * Returns the Logger used by the image processor.
     *
     * @return \Monolog\Logger The Logger instance.
     */
    public function getLogger() : \Monolog\Logger
    {
        // If there is no instance, use default instance.
        if ($this->logger == null) {
            $this->logger = new \Monolog\Logger($this->getTitle());
        }
                
        return $this->logger;
    }
}
