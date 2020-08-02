<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

interface ResultProcessorInterface
{
    /**
     * @return array<string>
     */
    public function getOutputLines(): array;

    public function getResultCollectionReadable(): ResultCollectorReadable;
}
