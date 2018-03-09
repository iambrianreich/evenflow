<?php

namespace EvenFlow;

use \Monolog\Logger;
use Evenflow\Workers\IWorker;
use Evenflow\Workers\Input\IWorkloadInput;
use Evenflow\Workers\Output\IWorkloadOutput;
use Evenflow\Workflows\SplitWorkflow;

class Workflow implements IWorker
{
    const LOGGER_NAME = 'evenflow-workflow';

    protected $workflow;
    protected $title;
    protected $logger;
    protected $stopOnError;
    protected $stopOnFailure;

    public function __construct(string $title, bool $stopOnFailure = false, bool $stopOnError = true, ?Logger $logger = null)
    {
        $this->workflow = [];
        $this->setTitle($title);
        $this->setStopOnError($stopOnError);
        $this->setStopOnFailure($stopOnFailure);

        if (! empty($logger)) {
            $this->setLogger($logger);
        }
    }

    public function setStopOnFailure(bool $stopOnFailure) : void
    {
        $this->stopOnFailure = $stopOnFailure;
    }

    public function getStopOnFailure() : bool
    {
        return $this->stopOnFailure;
    }

    public function setStopOnError(bool $stopOnError) : void
    {
        $this->stopOnError = $stopOnError;
    }

    public function getStopOnError() : bool
    {
        return $this->stopOnError;
    }

    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }
    
    public function getTitle() : string
    {
        return $this->title;
    }
    
    /**
     * Sets the Logger instance used used by the Workflow.
     *
     * @param \Monolog\Logger $logger The Logger.
     *
     * @return void
     */
    public function setLogger(\Monolog\Logger $logger) : void
    {
        $this->logger = $logger->withName(self::LOGGER_NAME);
    }
    
    /**
     * Returns the Logger used by the Workflow.
     *
     * @return \Monolog\Logger The Logger instance.
     */
    public function getLogger() : \Monolog\Logger
    {
        // If there is no instance, use default instance.
        if ($this->logger == null) {
            $this->logger = new \Monolog\Logger(self::LOGGER_NAME);
        }
                
        return $this->logger;
    }

    public function execute(IWorkloadInput $startingInput) : IWorkloadOutput
    {
        $input = $startingInput;
        foreach ($this->workflow as $worker) {
            $input = $worker->execute($input);

            if ($this->getStopOnFailure() && $input->isFailure()) {
                return $input;
            }
            if ($this->getStopOnError() && $input->isError()) {
                return $input;
            }
        }

        return $input;
    }

    public function then(IWorker $workload) : Workflow
    {
        $this->workflow[] = $workload;
        return $this;
    }
}
