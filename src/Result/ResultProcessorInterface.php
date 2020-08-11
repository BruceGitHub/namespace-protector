<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

interface ResultProcessorInterface extends ResultInterface
{
    /**
     * @return ResultCollectedReadable<ResultProcessedFile>
     */
    public function getProcessedResult(): ResultCollectedReadable;

    public function get(): string;
}
