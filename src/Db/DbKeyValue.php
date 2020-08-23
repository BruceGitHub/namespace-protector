<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Parser\Node\MatchedResultInterface;

final class DbKeyValue implements DbKeyValueInterface
{
    /** @var array<mixed> */
    private $collections;

    /**
     * @param array<string> $initValue
     */
    public function __construct(array $initValue = [])
    {
        $this->collections = $initValue;
    }

    public function add(string $key, string $value): void
    {
        $this->collections[$key] = $value;
    }

    public function booleanSearch(MatchCollectionInterface $match, Entry $matchMe): bool
    {
        /** @var MatchedResultInterface */
        $resul = $match->evaluate($this->collections, $matchMe);
        return $resul->matched();
    }

    public function count(): int
    {
        return \count($this->collections);
    }
}
