<?php declare(strict_types=1);

namespace NamespaceProtector\Result\Factory;

use NamespaceProtector\Result\ErrorResult;
use NamespaceProtector\Result\ResultCollected;

interface ErrorCollectionFactoryInterface
{
    /**
     * @return ResultCollected<ErrorResult>
     */
    public function createForErrorResult(): ResultCollected;
}
