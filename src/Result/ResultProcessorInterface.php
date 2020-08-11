<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

interface ResultProcessorInterface extends ResultInterface
{
    /**
     * @return ResultCollectorReadable<ResultProcessedFile>
     */
    public function getProcessedResult(): ResultCollectorReadable;

    public function get(): string;
}
