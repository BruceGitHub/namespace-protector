<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

use NamespaceProtector\Entry\Entry;

final class BooleanMatchKey implements MatchCollectionInterface
{
    /**
     * @param Iterable<mixed> $data
     */
    public function evaluate(iterable $data, Entry $matchMe): bool
    {
        /** @var array $data */
        if (\array_key_exists($matchMe->get(), $data)) {
            return true;
        }

        return false;
    }
}
