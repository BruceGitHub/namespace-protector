<?php

declare(strict_types=1);

namespace NamespaceProtector\Result\Factory;

use NamespaceProtector\Result\ResultCollected;
use NamespaceProtector\Result\ResultCollectedReadable;
use NamespaceProtector\Result\ResultCollectedInterface;
use NamespaceProtector\Result\ResultProcessedFileInterface;

interface CollectionFactoryInterface
{
    public function createMutableCollection(array $list): ResultCollected;

    public function createEmptyMutableCollection(): ResultCollected;

    public function createEmptyReadOnlyCollection(): ResultCollectedReadable;
}
