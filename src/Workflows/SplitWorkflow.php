<?php

namespace Evenflow\Workflows;

use Evenflow\Workflow;
use Evenflow\InvalidInputException;
use Evenflow\Workers\Output\OutputCollection;
use Evenflow\Workers\Input\IWorkloadInput;
use Evenflow\Workers\Output\IWorkloadOutput;
use Evenflow\Workers\Output\ICollection;

class SplitWorkflow extends Workflow
{
    public function execute(IWorkloadInput $startingInput) : IWorkloadOutput
    {
        // Make sure that we have a Collection.
        if (! $startingInput instanceof ICollection) {
            throw new InvalidInputException(
                "Split workflow expects a Collection."
            );
        }

        $outputs = new OutputCollection();

        $startingInputs = $startingInput->split();

        $this
            ->getLogger()
            ->debug(sprintf(
                'Splitting Worflow for %s workloads',
                count($startingInputs)
            ));

        // Iterate through each input and run the chain separately.
        foreach ($startingInputs as $currentStartingInput) {
            $input = $currentStartingInput;

            foreach ($this->workflow as $worker) {
                $this->getLogger()->info(
                    'Collecting input from ' . $worker->getTitle()
                );

                // Execute the next step in the workfow.
                $input = $worker->execute($input);

                // Add the result to the OutputCollection.
                $outputs->addOutput($input);

                // If stopOnFailure and the step failed, we're done.
                if ($this->getStopOnFailure() && $input->isFailure()) {
                    return $outputs;
                }
            
                // If stopOnError and the step threw an exception, we're done.
                if ($this->getStopOnError() && $input->isError()) {
                    return $outputs;
                }
            }
        }
        return $outputs;
    }
}
