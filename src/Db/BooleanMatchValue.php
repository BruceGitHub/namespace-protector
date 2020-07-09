<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

use NamespaceProtector\Entry\Entry;

final class BooleanMatchValue implements MatchCollectionInterface
{
    /**
     * @param Iterable<mixed> $data
     */
    public function evaluate(iterable $data, Entry $matchMe): bool
    {
        /** @var array $data */
        if (\in_array($matchMe->get(), $data, true)) {
            return true;
        }

        return false;
    }
}
