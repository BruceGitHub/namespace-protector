<?php

declare(strict_types=1);

namespace NamespaceProtector\Result\Factory;

use NamespaceProtector\Result\ResultCollected;
use NamespaceProtector\Result\ResultCollectedReadable;
use NamespaceProtector\Result\ResultCollectedInterface;
use NamespaceProtector\Result\ResultProcessedFileInterface;

interface CollectionFactoryInterface
{
    /**
     * @param array<int,\NamespaceProtector\Result\ResultProcessedFileInterface> $list
     * @return \NamespaceProtector\Result\ResultCollected<\NamespaceProtector\Result\ResultProcessedFileInterface>
     */
    public function createMutableCollection(array $list): ResultCollected;

    /**
     * @return \NamespaceProtector\Result\ResultCollected<\NamespaceProtector\Result\ResultProcessedFileInterface>
     */            
    public function createEmptyMutableCollection(): ResultCollected;

    /**
     * @return \NamespaceProtector\Result\ResultCollectedReadable<\NamespaceProtector\Result\ResultProcessedFileInterface>
     */            
    public function createEmptyReadOnlyCollection(): ResultCollectedReadable;
}
