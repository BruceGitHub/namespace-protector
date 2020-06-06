<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

class BooleanMatchKey implements MatchCollectionInterface
{
    /**
     * @param Iterable<mixed> $data
     */
    public function evaluate(iterable $data, string $matchMe): bool
    {
        /** @var array $data */
        if (\array_key_exists($matchMe, $data)) {
            return true;
        }

        return false;
    }
}
