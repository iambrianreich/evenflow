<?php

namespace Evenflow;

interface IWorkloadOutput
{
    public function isSuccessful() : bool;
    public function isFailure() : bool;
    public function isError() : bool;
    public function getException() : ?Exception;
}
