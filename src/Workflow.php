<?php

namespace EvenFlow;

class Workflow
{
    protected $workflow;
    protected $title;

    public function __construct(string $title)
    {
        $this->workflow = [];
        $this->setTitle($title);
    }

    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }
    
    public function getTitle() : string
    {
        return $this->title;
    }
    
    public function begin(IWorkloadInput $startingInput) : void
    {
        $input = $startingInput;
        $foreach($this->workflow as $worker) {
            $input = $workload->execute($startingInput);
        }
    }

    public function then(IWorker $workload) : Workflow
    {
        $this->workflow[] = $workload;
        return $this;
    }
}
