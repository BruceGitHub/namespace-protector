<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Parser\Node\MatchedResultInterface;

interface MatchCollectionInterface extends MatchInterface
{
    /**
     * @param Iterable<mixed> $data
     */
    public function evaluate(Iterable $data, Entry $matchMe): MatchedResultInterface;
}
