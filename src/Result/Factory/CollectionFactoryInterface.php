<?php

declare(strict_types=1);

namespace NamespaceProtector\Result\Factory;

use NamespaceProtector\Result\ResultCollected;
use NamespaceProtector\Result\ResultCollectedInterface;
use NamespaceProtector\Result\ResultProcessedFileInterface;

interface CollectionFactoryInterface
{
    /**
     * @return ResultCollected<ResultProcessedFileInterface>
     * @param array<ResultProcessedFileInterface> $list
     */
    public function createMutableCollection(array $list): ResultCollectedInterface;

    /**
     * @return ResultCollected<ResultProcessedFileInterface>
     */
    public function createEmptyMutableCollection(): ResultCollectedInterface;
}
