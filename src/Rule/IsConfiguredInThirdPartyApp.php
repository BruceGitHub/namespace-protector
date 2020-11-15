<?php declare(strict_types=1);

namespace NamespaceProtector\Rule;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Config\Config;
use NamespaceProtector\EnvironmentDataLoaderInterface;
use NamespaceProtector\Parser\Node\MatchedResultInterface;
use NamespaceProtector\Parser\Node\Event\EventProcessNodeInterface;

class IsConfiguredInThirdPartyApp implements RuleInterface
{
    private Config $config;
    private EnvironmentDataLoaderInterface $environmentDataLoader;

    public function __construct(
        EnvironmentDataLoaderInterface $environmentDataLoader,
        Config $config
    ) {
        $this->environmentDataLoader = $environmentDataLoader;
        $this->config = $config;
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
