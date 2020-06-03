<?php

namespace App\Result; 

use App\Result\Result;

final class ResultCollector   {
    private $listResult = [];

    public function __construct()
    {
        $this->listResult = [];
    }

    public function addResult(Result $result): void
    {
        $this->listResult[] = $result; 
    }

    public function get(): array
    {
        return $this->listResult;
    }

    public function empyResult(): void
    {
        $this->listResult = [];
    }

}