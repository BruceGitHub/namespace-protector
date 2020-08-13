<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

interface ResultProcessorInterface
{
    /**
     * @return ResultCollectedReadable<ResultProcessedFile>
     */
    public function getProcessedResult(): ResultCollectedReadable;
}
