<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Parser\Node\EmptyMatchedResult;
use NamespaceProtector\Parser\Node\MatchedResult;
use NamespaceProtector\Parser\Node\MatchedResultInterface;

final class BooleanMatchKey implements MatchCollectionInterface
{
    /**
     * @param Iterable<mixed> $data
     */
    public function evaluate(iterable $data, Entry $matchMe): MatchedResultInterface
    {
        /** @var array $data */
        if (\array_key_exists($matchMe->get(), $data)) {
            return new MatchedResult();
        }

        return new EmptyMatchedResult();
    }
}
