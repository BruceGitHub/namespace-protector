<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

interface ResultProcessorInterface
{
    /**
     * @return ResultCollectedReadable<ResultProcessedFileInterface>
     */
    public function getProcessedResult(): ResultCollectedReadable;
}
