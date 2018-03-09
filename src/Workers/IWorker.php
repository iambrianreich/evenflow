<?php
namespace EvenFlow\Workers;

use Evenflow\Workers\Input\IWorkloadInput;
use Evenflow\Workers\Output\IWorkloadOutput;

interface IWorker
{
    public function execute(IWorkloadInput $workload) : IWorkloadOutput;
}
