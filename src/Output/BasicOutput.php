<?php

namespace Evenflow\Workers\Output;

class BasicOutput implements IWorkloadOutput
{
    protected $success;
    protected $exception;
    protected $summary;

    
    public function __construct(bool $success = true, string $summary = '[none]', ?\Exception $exception = null)
    {
        $this->setSuccess($success);
        $this->setSummary($summary);
        $this->setException($exception);
    }

    public function setSuccess(bool $success) : void
    {
        $this->success = $success;
    }
    public function isSuccessful() : bool
    {
        return $this->success;
    }

    public function isFailure() : bool
    {
        return ! $this->isSuccessful();
    }
    public function isError() : bool
    {
        return $this->getException() != null;
    }
    public function setException(\Exception $exception = null) : void
    {
        $this->exception = $exception;
    }

    public function getException() : ?\Exception
    {
        return $this->exception;
    }

    public function setSummary(string $summary) : void
    {
        $this->summary = $summary;
    }
    public function getSummary() : string
    {
        return $this->summary;
    }
}
