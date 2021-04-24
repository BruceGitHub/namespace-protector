<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultProcessor implements ResultProcessorInterface
{
    /**
     * @param ResultCollectedReadable<ResultProcessedFileInterface> $resultCollectorReadable
     */
    public function __construct(private ResultCollectedReadable $resultCollectorReadable)
    {
    }

    public function getProcessedResult(): ResultCollectedReadable
    {
        return $this->resultCollectorReadable;
    }
}
