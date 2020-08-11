<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

interface ResultParserInterface extends ResultInterface
{
    public function append(ResultParserInterface $toAppendInstance): void;

    /** @return ResultCollectorReadable<ResultParserInterface> */
    public function getResultCollectionReadable(): ResultCollectorReadable;
}
