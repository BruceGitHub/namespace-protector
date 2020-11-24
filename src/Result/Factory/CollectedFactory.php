<?php

declare(strict_types=1);

namespace NamespaceProtector\Result\Factory;

use NamespaceProtector\Result\ResultCollected;
use NamespaceProtector\Result\ResultCollectedReadable;

final class CollectedFactory implements CollectionFactoryInterface
{
    public function createMutableCollection(array $list): ResultCollected
    {
        return new ResultCollected($list);
    }

    public function createEmptyMutableCollection(): ResultCollected
    {
        return new ResultCollected();
    }

    public function createEmptyReadOnlyCollection(): ResultCollectedReadable
    {
        return new ResultCollectedReadable($this->createEmptyMutableCollection());
    }
}
