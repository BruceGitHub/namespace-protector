<?php

namespace NamespaceProtector\Result;

final class ResultCollector
{

    /** @var array<Result>  */
    private $listResult ;

    public function __construct()
    {
        $this->listResult = [];
    }

    public function addResult(Result $result): void
    {
        $this->listResult[] = $result;
    }

    /** @return  array<Result>  */
    public function get(): array
    {
        return $this->listResult;
    }

    public function emptyResult(): void
    {
        $this->listResult = [];
    }
}
