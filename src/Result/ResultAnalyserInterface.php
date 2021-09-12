<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use Countable;

interface ResultAnalyserInterface extends Countable
{
    public function append(ResultProcessedFileInterface $toAppend): void;

    public function count(): int;

    public function getResultCollected(): ResultCollectedReadable;
}
