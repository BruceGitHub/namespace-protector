<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

use NamespaceProtector\Entry\Entry;

class BooleanMatchPos implements MatchCollectionInterface
{
    /**
     * @param Iterable<mixed> $data
     */
    public function evaluate(Iterable $data, Entry $matchMe): bool
    {   
        foreach ($data as $entry => $value) {
            if (strpos($matchMe->get(), $entry) !==false) {
                return true;
            }
        }

        return false;
    }
}
