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
    private $resultCollectedImmutable;

    /**
     * @param ResultCollectedInterface<T> $resultCollectedImmutable
     */
    public function __construct(ResultCollectedInterface $resultCollectedImmutable)
    {
        $this->resultCollectedImmutable = $resultCollectedImmutable;
    }

    public function count(): int
    {
        return \count($this->resultCollectedImmutable);
    }

    /**
     * @return Iterator<T>
     */
    public function getIterator(): Iterator
    {
        return $this->resultCollectedImmutable->getIterator();
    }
}
