<?php

namespace Evenflow\Workers\Output;

interface IWorkloadOutput
{
    public function isSuccessful() : bool;
    public function isFailure() : bool;
    public function isError() : bool;
    public function getSummary() : string;
    
    public function getException() : ?\Exception;
}
