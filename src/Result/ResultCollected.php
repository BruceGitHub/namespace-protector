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
    /** @var  array<int, T> */
    private array $list;

    /**
     * @param  array<int, T> $list
     */
    public function __construct(array $list = [])
    {
        $this->list = $list;
    }

    /**
     * @param T $list
     * @return void
     */
    public function addResult($list): void
    {
        $this->list[] = $list;
    }

    public function count(): int
    {
        return \count($this->list);
    }

    public function emptyResult(): void
    {
        $this->list = [];
    }

    /**
     * @return Iterator<T>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->list);
    }
}
