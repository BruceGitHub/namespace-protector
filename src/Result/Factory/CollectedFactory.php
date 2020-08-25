<?php declare(strict_types=1);

namespace NamespaceProtector\Result\Factory;

use NamespaceProtector\Result\ErrorResult;
use NamespaceProtector\Result\ResultCollected;
use NamespaceProtector\Result\ResultProcessedFileEditable;
use NamespaceProtector\Result\ResultProcessedFileInterface;

final class CollectedFactory
{
    /**
     * @return ResultCollected<ResultProcessedFileInterface>
     * @param array<ResultProcessedFileEditable> $list
     */
    public function createForProcessdFile(array $list = []): ResultCollected
    {
        /** @var array<ResultProcessedFileInterface> $list */
        return  new ResultCollected($list);
    }

    /**
     * @return ResultCollected<ErrorResult>
     */
    public function createForErrorResult(): ResultCollected
    {
        return  new ResultCollected();
    }
}
