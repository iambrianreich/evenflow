<?php

namespace Evenflow\WorkloadInput;

use Evenflow\IWorkloadInput;

class StringInput implements IWorkloadInput
{
    public function setData(string $data) : void
    {
        $this->data = $data;
    }

    public function getData() : string
    {
        return $this->data;
    }
}
