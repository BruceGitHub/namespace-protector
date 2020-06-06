<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

final class DbKeyValue implements DbKeyValueInterface
{
    /** @var array<mixed> */
    private $collections;

    public function __construct()
    {
        $this->collections = [];
    }

    public function add(string $key, string $value): void
    {
        $this->collections[$key] = $value;
    }

    public function booleanSearch(MatchCollectionInterface $match, string $matchMe): bool
    {
        if ($match->evaluate($this->collections, $matchMe)) {
            return true;
        }

        return false;
    }

    public function count(): int
    {
        return \count($this->collections);
    }
}
