<?php
declare(strict_types=1);

namespace NamespaceProtector\Db;

interface MatchCollectionInterface extends MatchInterface
{
    /**
     * @param Iterable<mixed> $data
     */
    public function evaluate(Iterable  $data, string $matchMe): bool;
}
