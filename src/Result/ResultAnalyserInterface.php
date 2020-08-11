<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use Countable;

interface ResultAnalyserInterface extends Countable
{
    public function append(ResultAnalyserInterface $toAppendInstance): ResultAnalyserInterface;

    public function withResults(): bool;

    public function count(): int;

    public function getResultCollector(): ResultCollectedReadable;
}
