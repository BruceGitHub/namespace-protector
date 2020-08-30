<?php declare(strict_types=1);

namespace NamespaceProtector\Result\Factory;

use NamespaceProtector\Result\ResultCollected;
use NamespaceProtector\Result\ResultCollectedInterface;
use NamespaceProtector\Result\ResultProcessedFileInterface;

abstract class AbstractCollectionFactory
{
    /**
     * @return ResultCollected<ResultProcessedFileInterface>
     * @param array<ResultProcessedFileInterface> $list
     */
    abstract public function createChangeableProcessedFile(array $list): ResultCollectedInterface;

    /**
     * @return ResultCollected<ResultProcessedFileInterface>
     */
    abstract public function createEmptyChangeableProcessedFile(): ResultCollectedInterface;
}
