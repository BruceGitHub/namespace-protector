<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use Iterator;
use ArrayIterator;

/**
 * @implements ResultCollectorInterface<ResultInterface>
 */
final class ResultCollected implements ResultCollectorInterface
{
    /** @var array<ResultInterface>  */
    private $listResult;

    /**
     * @param array<ResultInterface> $result
     */
    public function __construct(array $result = [])
    {
        $this->listResult = $result;
    }

    public function addResult(ResultInterface $result): void
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
     * @return Iterator<ResultInterface>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->listResult);
    }
}
