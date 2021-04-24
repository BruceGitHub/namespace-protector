<?php

declare(strict_types=1);

namespace NamespaceProtector\Metadata;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Db\DbKeyValue;
use NamespaceProtector\Db\MatchCollectionInterface;
use NamespaceProtector\Parser\Node\EmptyMatchedResult;
use NamespaceProtector\Parser\Node\MatchedResult;
use NamespaceProtector\Parser\Node\MatchedResultInterface;

class VendorNamespace implements VendorNamespaceInterface
{
    private DbKeyValue $collection;

    public function __construct(private MatchCollectionInterface $macher)
    {
        $this->collection = new DbKeyValue();
    }

    public function load(): void
    {
    }

    public function hasNamespace(Entry $entry): MatchedResultInterface
    {
        if ($this->collection->booleanSearch($this->macher, $entry)) {
            return new MatchedResult($entry->get());
        }

        return new EmptyMatchedResult();
    }
}
