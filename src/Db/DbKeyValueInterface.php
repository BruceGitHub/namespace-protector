<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

use NamespaceProtector\Entry\Entry;

interface DbKeyValueInterface extends DbInterface
{
    public function add(string $key, string $value): void;
    public function booleanSearch(MatchCollectionInterface $match, Entry $matchMe): bool;
    public function count(): int;
}
