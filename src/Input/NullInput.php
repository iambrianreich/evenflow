<?php

namespace Evenflow\Workers\Input;

use Evenflow\Workers\Input\IWorkloadInput;

class NullInput implements IWorkloadInput
{
    public function getData() : mixed
    {
        return null;
    }
}
