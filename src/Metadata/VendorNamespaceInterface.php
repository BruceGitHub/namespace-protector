<?php declare(strict_types=1);

namespace NamespaceProtector\Metadata;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Parser\Node\MatchedResultInterface;

interface VendorNamespaceInterface
{
    public function load(): void;

    public function hasNamespace(Entry $entry): MatchedResultInterface;
}
