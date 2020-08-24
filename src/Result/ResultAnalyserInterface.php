<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use Countable;

interface ResultAnalyserInterface extends Countable
{
    public function append(ResultAnalyserInterface $toAppendInstance): void;

    public function withResults(): bool;

    public function count(): int;

    /**
     * @return ResultCollectedReadable<ResultProcessedFileInterface>
     */
    public function getResultCollected(): ResultCollectedReadable;
}
