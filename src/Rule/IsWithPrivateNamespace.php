<?php

declare(strict_types=1);

namespace NamespaceProtector\Rule;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Config\Config;
use NamespaceProtector\Db\BooleanMatchNameSpace;
use NamespaceProtector\Db\MatchCollectionInterface;
use NamespaceProtector\Parser\Node\MatchedResultInterface;
use NamespaceProtector\Parser\Node\Event\EventProcessNodeInterface;

class IsWithPrivateNamespace implements RuleInterface
{
    public function __construct(
        private Config $config,
        private IsInConfigureComposerPsr4 $isInConfigureComposerPsr4
    ) {
    }

    public function apply(Entry $entry, EventProcessNodeInterface $event): bool
    {
        if ($this->config->getMode() !== Config::MODE_MAKE_VENDOR_PRIVATE) {
            return false;
        }

        if ($this->isInPublicConfiguredEntries($entry, new BooleanMatchNameSpace())->matched()) {
            return true;
        }

        if ($this->isInConfigureComposerPsr4->apply($entry, $event)) {
            return true;
        }

        $event->foundError();
        return true;
    }

    private function isInPublicConfiguredEntries(Entry $currentNamespaceAccess, MatchCollectionInterface $macher): MatchedResultInterface
    {
        return $macher->evaluate($this->config->getPublicEntries(), $currentNamespaceAccess);
    }
}
