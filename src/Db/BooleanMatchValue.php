<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

class BooleanMatchValue implements MatchCollectionInterface
{
    /**
     * @param Iterable<mixed> $data
     */
    public function evaluate(iterable $data, string $matchMe): bool
    {
        /** @var array $data */
        if (\in_array($matchMe, $data, true)) {
            return true;
        }

        return false;
    }
}
