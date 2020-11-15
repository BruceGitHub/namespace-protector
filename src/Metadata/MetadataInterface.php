<?php declare(strict_types=1);

namespace NamespaceProtector\Metadata;

use Countable;
use NamespaceProtector\Db\DbKeyValue;

interface MetadataInterface extends Countable
{
    public function hasMetadata(DbKeyValue $dbKeyValue): DbKeyValue;

    public function metadataIterator(): iterable;

    public function count(): int;

    public function load();
}
