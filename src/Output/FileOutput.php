<?php

namespace Evenflow\Workers\Output;

use Evenflow\Workers\Output\BasicOutput;
use Evenflow\Workers\Input\IWorkloadInput;

class FileOutput extends BasicOutput implements IWorkloadInput
{
    protected $file;

    public function __construct(string $file, bool $success = true, string $summary = '[none]', ?\Exception $exception = null)
    {
        parent::__construct($success, $summary, $exception);
        $this->setFile($file);
    }

    public function setFile(string $file) : void
    {
        $this->file = $file;
    }

    public function getFile() : string
    {
        return $this->file;
    }

    public function fileExists() : bool
    {
        return file_exists($this->getFile());
    }

    public function getData()
    {
        return $this->getFile();
    }
}
