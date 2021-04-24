<?php

declare(strict_types=1);

namespace NamespaceProtector\Rule;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Config\Config;
use NamespaceProtector\EnvironmentDataLoaderInterface;
use NamespaceProtector\Parser\Node\MatchedResultInterface;
use NamespaceProtector\Parser\Node\Event\EventProcessNodeInterface;

class IsConfiguredInThirdPartyApp implements RuleInterface
{
    public function __construct(
        private EnvironmentDataLoaderInterface $environmentDataLoader,
        private Config $config
    ) {
    }

    public function apply(Entry $entry, EventProcessNodeInterface $event): bool
    {
        if ($this->config->getMode() !== Config::MODE_AUTODISCOVER) {
            return false;
        }

        if (!$this->check($entry)->matched()) {
            return true;
        }

        $event->foundError();
        return true;
    }

    private function check(Entry $currentNamespaceAccess): MatchedResultInterface
    {
        return $this->environmentDataLoader->vendorNamespaces()->hasNamespace($currentNamespaceAccess);
    }
}
