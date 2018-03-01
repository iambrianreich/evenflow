<?php
namespace EvenFlow;

interface IWorker
{
    public function execute(IWorkloadInput $workload) : IWorkloadOutput
}
