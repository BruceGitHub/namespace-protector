<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use Iterator;
use ArrayIterator;

/**
 * @template T
 * @implements ResultCollectedInterface<T>
 */
final class ResultCollected implements ResultCollectedInterface
{
    /** @var array<T>  */
    private $listResult;

    /**
     * @param array<T> $result
     */
    public function __construct(array $result = [])
    {
        $this->listResult = $result;
    }

    /**
     * @param T $result
     * @return void
     */
    public function addResult($result): void
    {
        $this->listResult[] = $result;
    }

    public function count(): int
    {
        return \count($this->listResult);
    }

    public function emptyResult(): void
    {
        $this->listResult = [];
    }

    /**
     * @return Iterator<T>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->listResult);
    }
}
