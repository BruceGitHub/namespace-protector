<?php

declare(strict_types=1);

namespace NamespaceProtector\Rule;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Db\BooleanMatchNameSpace;
use NamespaceProtector\EnvironmentDataLoaderInterface;
use NamespaceProtector\Parser\Node\Event\EventProcessNodeInterface;

class IsInConfigureComposerPsr4 implements RuleInterface
{
    public function __construct(private EnvironmentDataLoaderInterface $metadataLoader)
    {
    }

    public function apply(Entry $entry, EventProcessNodeInterface $event): bool
    {
        $val = $this->stripFirstSlash($entry);

        return $this->metadataLoader->getCollectComposerNamespace()->booleanSearch(new BooleanMatchNameSpace(), $val);
    }

    private function stripFirstSlash(Entry $token): Entry
    {
        if ($token->get()[0] === '\\') {
            return new Entry(substr($token->get(), 1, strlen($token->get())));
        }

        return $token;
    }
}
