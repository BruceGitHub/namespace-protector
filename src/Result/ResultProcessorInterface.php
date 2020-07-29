<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

interface ResultProcessorInterface
{
    public function getOutputLines(): array;

    public function getResultCollectionReadable(): ResultCollectorReadable;
}
