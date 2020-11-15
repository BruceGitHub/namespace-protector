<?php declare(strict_types=1);

namespace NamespaceProtector\Metadata;

use Countable;

interface MetadataInterface extends Countable
{
    public function metadataIterator(): iterable;

    public function count(): int;

    public function load();
}
