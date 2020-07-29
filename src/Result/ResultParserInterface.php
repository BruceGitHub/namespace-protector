<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

interface ResultParserInterface
{
    public function append(ResultParserInterface $toAppendInstance): ResultParserInterface;

    public function getResultCollectionReadable(): ResultCollectorReadable;
}
