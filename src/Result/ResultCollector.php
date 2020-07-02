<?php

namespace NamespaceProtector\Result;

final class ResultCollector
{
    /** @var array<ResultInterface>  */
    private $listResult ;

    public function __construct()
    {
        $this->listResult = [];
    }

    public function addResult(ResultInterface $result): void
    {
        $this->listResult[] = $result;
    }

    /** @return  array<ResultInterface>  */
    public function get(): array
    {
        return $this->listResult;
    }

    public function emptyResult(): void
    {
        $this->listResult = [];
    }
}
