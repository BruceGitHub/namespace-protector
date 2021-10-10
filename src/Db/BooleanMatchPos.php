<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Parser\Node\MatchedResult;
use NamespaceProtector\Parser\Node\EmptyMatchedResult;
use NamespaceProtector\Parser\Node\MatchedResultInterface;

final class BooleanMatchPos implements MatchCollectionInterface
{
    /**
     * @param Iterable<mixed> $data
     */
    public function evaluate(Iterable $data, Entry $matchMe): MatchedResultInterface
    {
        /**
         * @var string $entry
         * @var string $value
         */
        foreach ($data as $entry => $value) { //todo: remove

            if ($value) {
                //todo: no-op
                //this interface will change! soon
            }

            if (\str_contains($matchMe->get(), $entry) === true) {
                return new MatchedResult();
            }
        }

        return new EmptyMatchedResult();
    }
}
