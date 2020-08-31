<?php declare(strict_types=1);

namespace NamespaceProtector\Result\Factory;

use NamespaceProtector\Result\ResultCollected;

final class ErrorCollectionFactory implements ErrorCollectionFactoryInterface
{
    public function createForErrorResult(): ResultCollected
    {
        return new ResultCollected();
    }
}
