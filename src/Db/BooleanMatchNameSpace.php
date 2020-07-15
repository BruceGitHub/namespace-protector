<?php

declare(strict_types=1);

namespace NamespaceProtector\Db;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Parser\Node\EmptyMatchedResult;
use NamespaceProtector\Parser\Node\MatchedResult;
use NamespaceProtector\Parser\Node\MatchedResultInterface;

final class BooleanMatchNameSpace implements MatchCollectionInterface
{
    /**
     * @param Iterable<mixed> $data
     */
    public function evaluate(iterable $data, Entry $matchMe): MatchedResultInterface
    {

        foreach ($data as $item) {
            $currentEntry = \strtolower($item);
            $current = \strtolower($matchMe->get());

            if ($this->isCurrentNamespaceInsideAPublicNamespace($current, $currentEntry)) {
                return new MatchedResult($item);
            }
        }

        return new EmptyMatchedResult();
    }

    private function isCurrentNamespaceInsideAPublicNamespace(string $current, string $publicEntry): bool
    {        

        if ($current === $publicEntry) {
            return true;
        }

        $blockEntry = \explode('\\', $publicEntry);
        $blockCurrent = \explode('\\', $current);

        foreach ($blockCurrent as $tokenCurrent) {
            if ($tokenCurrent === '') {
                continue;
            }

            if (!\in_array($tokenCurrent, $blockEntry, true)) {
                return false;
            }
        }

        return \true;
    }
}
