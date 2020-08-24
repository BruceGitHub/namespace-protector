<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use Iterator;
use Countable;
use IteratorAggregate;

/**
 * @template T
 * @extends IteratorAggregate<T>
 */
interface ResultCollectedInterface extends Countable, IteratorAggregate
{
    public function count(): int;

    /** @return Iterator<T> */
    public function getIterator(): Iterator;
}
