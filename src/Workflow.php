<?php

/**
 * This file contains the EvenFlow\Workflow class.
 *
 * @author     Brian Reich <breich@reich-consulting.net>
 * @copyright  Copyright (C) 2018 Reich Web Consulting
 * @license    Proprietary
 */

namespace EvenFlow;

use \Monolog\Logger;
use Evenflow\Workers\IWorker;
use Evenflow\Workers\Input\IWorkloadInput;
use Evenflow\Workers\Output\IWorkloadOutput;
use Evenflow\Workflows\SplitWorkflow;

/**
 * A Workflow describes a series of tasks that operate on each other's output.
 *
 * A Workflow is created by calling it's constructor and passing several options
 * to configure it. The $title parameter simply names the workflow. The second
 * parameter, called $stopOnFailure, specifies that the Workflow and all of the
 * steps that it contains, must halt processing when a failure occurs (it's
 * returned failure returns true from isFailure()). The third parameter, $logger
 * may specify a Logger instance for monitoring the Workflow.
 *
 * After instantiation the Workflow has no steps and executing it will not
 * produce any work. Use the then() method to add IWorkers to the Workflow.
 *
 * When you are finised building your Workflow, call your Workflow's
 * execute() method and pass it an IWorkloadInput compatible with your first
 * IWorker's implementation.
 *
 * @author     Brian Reich <breich@reich-consulting.net>
 * @copyright  Copyright (C) 2018 Reich Web Consulting
 * @license    Proprietary
 */
class Workflow implements IWorker
{
    /**
     * The default logger name.
     *
     * @var  string
     */
    const LOGGER_NAME = 'evenflow-workflow';

    /**
     * The list of IWorkers in the Workflow.
     *
     * @var array
     */
    protected $workflow;

    /**
     * The name of the Workflow.
     *
     * @var string
     */
    protected $title;

    /**
     * A Logger for tracking Workflow progress.
     *
     * @var string
     */
    protected $logger;

    /**
     * True to stop execution of steps when an error is detected.
     *
     * @var boolean
     */
    protected $stopOnError;

    /**
     * True to stop execution of steps when a failure is detected.
     *
     * @var boolean
     */
    protected $stopOnFailure;

    /**
     * Creates a new Workflow.
     *
     * @param string       $title         The name of the Workflow.
     * @param bool|boolean $stopOnFailure True to stop execution on failure.
     * @param bool|boolean $stopOnError   True to stop execution on error.
     * @param Logger|null  $logger        A Logger instance used to track progress.
     */
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

    /**
     * Set to true to stop execute() when a failure occurs.
     *
     * The setStopOnFailure() method configures the Workflow's behavior when a
     * failure is detected in the value returned from an executed IWorker. If
     * set to true, the Workflow will stop and return the end value of the
     * Workflow. If set to false, the Workflow will continue to execute and
     * pass the failed IWorker return value to the next IWorker.
     *
     * @param bool $stopOnFailure True to stop on failure.
     */
    public function setStopOnFailure(bool $stopOnFailure) : void
    {
        $this->stopOnFailure = $stopOnFailure;
    }

    /**
     * Returns true if the Workflow should stop executing on failure.
     *
     * @return boolean Returns true if the Workflow should stop executing on failure.
     */
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
