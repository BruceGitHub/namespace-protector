<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use Iterator;
use Countable;

/**
 * @template T
 * @implements ResultCollectedInterface<T>
 */
final class ResultCollectedReadable implements Countable, ResultCollectedInterface
{
    /** @var ResultCollectedInterface<T> */
    private $resultCollector;

    /**
     * @param ResultCollectedInterface<T> $resultCollector
     */
    public function __construct(ResultCollectedInterface $resultCollector)
    {
        $this->resultCollector = $resultCollector;
    }

    public function count(): int
    {
        return \count($this->resultCollector);
    }

    /**
     * @return Iterator<T>
     */
    public function getIterator(): Iterator
    {
        return $this->resultCollector->getIterator();
    }
}
