<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

interface DbKeyValueInterface extends DbInterface
{
    public function add(string $key, string $value): void;
    public function booleanSearch(MatchCollectionInterface $match, string $matchMe): bool;
    public function count(): int;
}
