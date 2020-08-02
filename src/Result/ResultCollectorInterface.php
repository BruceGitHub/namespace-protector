<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

use Countable;

interface ResultCollectorInterface extends Countable
{
    /** @return  array<ResultInterface>  */
    public function get(): iterable;

    public function count(): int;
}
