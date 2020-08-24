<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultProcessor implements ResultProcessorInterface
{
    /** @var ResultCollectedReadable<ResultProcessedFileInterface> */
    private $resultCollectorReadable;

    /**
     * @param ResultCollectedReadable<ResultProcessedFileInterface> $resultCollectorReadable
     */
    public function __construct(ResultCollectedReadable $resultCollectorReadable)
    {
        $this->resultCollectorReadable = $resultCollectorReadable;
    }

    public function getProcessedResult(): ResultCollectedReadable
    {
        return $this->resultCollectorReadable;
    }
}
