<?php

declare(strict_types=1);

namespace NamespaceProtector\Rule;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Db\BooleanMatchKey;
use NamespaceProtector\Db\BooleanMatchValue;
use NamespaceProtector\Db\DbKeyValueInterface;
use NamespaceProtector\Db\MatchCollectionInterface;
use NamespaceProtector\EnvironmentDataLoaderInterface;
use NamespaceProtector\Parser\Node\Event\EventProcessNodeInterface;

class FalsePositive implements RuleInterface
{
    public function __construct(private EnvironmentDataLoaderInterface $metadataLoader)
    {
    }

    public function apply(Entry $entry, EventProcessNodeInterface $event): bool
    {
        $entry = $this->stripFirstSlash($entry);

        if ($this->valueExist($this->metadataLoader->getCollectBaseConstants(), new BooleanMatchKey(), $entry)) {
            return true;
        }

        if ($this->valueExist($this->metadataLoader->getCollectBaseFunctions(), new  BooleanMatchValue(), $entry)) {
            return true;
        }

        if ($this->valueExist($this->metadataLoader->getCollectBaseInterfaces(), new  BooleanMatchValue(), $entry)) {
            return true;
        }

        if ($this->valueExist($this->metadataLoader->getCollectBaseClasses(), new  BooleanMatchValue(), $entry)) {
            return true;
        }

        return false;
    }

    private function valueExist(DbKeyValueInterface $collections, MatchCollectionInterface $matchCriteria, Entry $matchMe): bool
    {
        if ($collections->booleanSearch($matchCriteria, $matchMe)) {
            return true;
        }

        return false;
    }

    private function stripFirstSlash(Entry $token): Entry
    {
        if ($token->get()[0] === '\\') {
            return new Entry(substr($token->get(), 1, strlen($token->get())));
        }

        return $token;
    }
}
