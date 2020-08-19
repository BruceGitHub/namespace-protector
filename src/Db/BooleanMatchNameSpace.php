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
    public function evaluate(iterable $data, Entry $matchMeInData): MatchedResultInterface
    {
        foreach ($data as $item) {
            $currentEntry = \strtolower($this->stripQueueSlash($item));
            $matchMeLiteral = \strtolower($this->stripQueueSlash($matchMeInData->get()));

            if ($this->isMatchMeNameSpaceInCheckEntry($matchMeLiteral, $currentEntry)) {
                return new MatchedResult($item);
            }
        }

        return new EmptyMatchedResult();
    }

    private function isMatchMeNameSpaceInCheckEntry(string $matchMeInData, string $checkEntry): bool
    {
        if ($matchMeInData === $checkEntry) {
            return true;
        }

        $pos = \strpos($matchMeInData, $checkEntry);
        if ($pos === false) {
            return false;
        }

        if (\strlen($checkEntry) > \strlen($matchMeInData)) {
            return false;
        }

        return true;
    }

    private function stripQueueSlash(string $token): string
    {
        $tokenOne = ltrim($token, '\\');
        $tokenTwo = rtrim($tokenOne, '\\');

        return $tokenTwo;
    }
}
