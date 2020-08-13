<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultProcessor implements ResultProcessorInterface
{
    /** @var ResultCollectedReadable<ResultProcessedFile> */
    private $resultCollectorReadable;

    /**
     * @param ResultCollectedReadable<ResultProcessedFile> $resultCollectorReadable
     */
    public function __construct(ResultCollectedReadable $resultCollectorReadable)
    {
        $this->resultCollectorReadable = $resultCollectorReadable;
    }

    /**
     * @return ResultCollectedReadable<ResultProcessedFile>
     */
    public function getProcessedResult(): ResultCollectedReadable
    {
        return $this->resultCollectorReadable;
    }
}
