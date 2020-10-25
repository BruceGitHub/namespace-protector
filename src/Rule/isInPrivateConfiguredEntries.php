<?php declare(strict_types=1);

namespace NamespaceProtector\Rule;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Config\Config;
use NamespaceProtector\Db\BooleanMatchNameSpace;
use NamespaceProtector\Parser\Node\MatchedResult;
use NamespaceProtector\Db\MatchCollectionInterface;
use NamespaceProtector\Parser\Node\MatchedResultInterface;
use NamespaceProtector\Parser\Node\Event\EventProcessNodeInterface;

class isInPrivateConfiguredEntries implements RuleInterface
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function apply(Entry $entry, EventProcessNodeInterface $event): bool
    {
        /** @var MatchedResult */
        $result = $this->isInPrivateConfiguredEntries($entry, new BooleanMatchNameSpace());

        if ($result->matched()) {
            $event->foundError($result->getInfo());
            return true;
        }

        return false;
    }

    private function isInPrivateConfiguredEntries(Entry $currentNamespaceAccess, MatchCollectionInterface $macher): MatchedResultInterface
    {
        return $macher->evaluate($this->config->getPrivateEntries(), $currentNamespaceAccess);
    }
}
