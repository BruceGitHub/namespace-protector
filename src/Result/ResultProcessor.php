<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultProcessor implements ResultProcessorInterface
{
    /** @var ResultCollectorReadable<ResultProcessedFile> */
    private $resultCollectorReadable;

    /**
     * @param ResultCollectorReadable<ResultProcessedFile> $resultCollectorReadable
     */
    public function __construct(ResultCollectorReadable $resultCollectorReadable)
    {
        $this->resultCollectorReadable = $resultCollectorReadable;
    }

    public function get(): string
    {
        return '';
    }

    /**
     * @return ResultCollectorReadable<ResultProcessedFile>
     */
    public function getProcessedResult(): ResultCollectorReadable
    {
        return $this->resultCollectorReadable;
    }
}
