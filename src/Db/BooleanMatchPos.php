<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

class BooleanMatchPos implements MatchCollectionInterface
{
    /**
     * @param Iterable<mixed> $data
     */
    public function evaluate(Iterable $data, string $matchMe): bool
    {   
        foreach ($data as $entry => $value) {
            if (strpos($matchMe, $entry) !==false) {
                return true;
            }
        }

        return false;
    }
}
