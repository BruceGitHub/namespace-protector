<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

use NamespaceProtector\Entry\Entry;

final class BooleanMatchNameSpace implements MatchCollectionInterface
{
    /**
     * @param Iterable<mixed> $data
     */
    public function evaluate(iterable $data, Entry $matchMe): bool
    {
        foreach ($data as $item) {
            $currentEntry = \strtolower($item);
            $current = \strtolower($matchMe->get());
            if ($this->isFullyQualifiedNamespaceValid($current, $currentEntry)) {
                return true;
            }
        }

        return false;
    }

    private function isFullyQualifiedNamespaceValid(string $current, string $publicEntry): bool
    {
        if (strpos($current, $publicEntry) !== false) {
            $blockEntry = \explode('\\', $publicEntry);
            $blockCurrent = \explode('\\', $current);

            $endA = \end($blockEntry);
            $endB = \end($blockCurrent);

            if ($endA !== $endB) {
                return false;
            }

            return true;
        }

        return false;
    }
}
