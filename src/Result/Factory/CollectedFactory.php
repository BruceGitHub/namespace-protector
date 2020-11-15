<?php

declare(strict_types=1);

namespace NamespaceProtector\Result\Factory;

use NamespaceProtector\Result\ErrorResult;
use NamespaceProtector\Result\ResultCollected;
use NamespaceProtector\Result\ResultCollectedInterface;

final class CollectedFactory implements CollectionFactoryInterface
{
    /**
     * @return ResultCollected<ErrorResult>
     */
    public function createForErrorResult(): ResultCollected //todo: move to another factory
    {
        return new ResultCollected();
    }

    public function createMutableCollection(array $list): ResultCollectedInterface
    {
        return new ResultCollected($list);
    }

    public function createEmptyMutableCollection(): ResultCollectedInterface
    {
        return  new ResultCollected();
    }
}
