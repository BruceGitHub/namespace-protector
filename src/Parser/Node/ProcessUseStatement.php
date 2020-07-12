<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Config\Config;
use NamespaceProtector\Db\BooleanMatchKey;
use NamespaceProtector\Db\BooleanMatchNameSpace;
use NamespaceProtector\Db\BooleanMatchValue;
use NamespaceProtector\Db\DbKeyValueInterface;
use NamespaceProtector\Db\MatchCollectionInterface;
use NamespaceProtector\EnvironmentDataLoaderInterface;
use NamespaceProtector\Parser\Node\Event\EventProcessNodeInterface;

final class ProcessUseStatement
{
    /** @var EnvironmentDataLoaderInterface  */
    private $metadataLoader;

    /** @var Config  */
    private $globalConfig;

    public function __construct(EnvironmentDataLoaderInterface $metadataLoader, Config $configGlobal)
    {
        $this->globalConfig = $configGlobal;
        $this->metadataLoader = $metadataLoader;
    }

    public function __invoke(EventProcessNodeInterface $event): void
    {
        $val = new Entry($event->getNodeName());
        if ($this->isFalsePositives($val)) {
            return;
        }

        if ($this->globalConfig->getMode() === Config::MODE_MAKE_VENDOR_PRIVATE) {
            $this->withModeVendorPrivate($val, $event);
            return;
        }

        if (true === $this->isInConfiguredComposerPsr4Namespaces($val, new BooleanMatchNameSpace())) {
            return;
        }

        if (true === $this->isInPrivateConfiguredEntries($val, new BooleanMatchNameSpace())) {
            $event->foundError();
            return;
        }
    }

    private function withModeVendorPrivate(Entry $currentNamespaceAccess, EventProcessNodeInterface $event): void
    {
        if ($this->isInPublicConfiguredEntries($currentNamespaceAccess, new BooleanMatchNameSpace())) {
            return;
        }

        if ($this->isInConfiguredComposerPsr4Namespaces($currentNamespaceAccess, new BooleanMatchNameSpace())) {
            return;
        }

        $event->foundError();
        return;
    }

    private function isFalsePositives(Entry $resultTocheck): bool
    {
        $result = $this->stripFirstSlash($resultTocheck);

        if ($this->valueExist($this->metadataLoader->getCollectBaseConstants(), new BooleanMatchKey(), $result)) {
            return true;
        }

        if ($this->valueExist($this->metadataLoader->getCollectBaseFunctions(), new  BooleanMatchValue(), $result)) {
            return true;
        }

        if ($this->valueExist($this->metadataLoader->getCollectBaseInterfaces(), new  BooleanMatchValue(), $result)) {
            return true;
        }

        if ($this->valueExist($this->metadataLoader->getCollectBaseClasses(), new  BooleanMatchValue(), $result)) {
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

    //todo: Use MatchedResultInterface
    private function isInPublicConfiguredEntries(Entry $currentNamespaceAccess, MatchCollectionInterface $macher): bool
    {
        return $macher->evaluate($this->globalConfig->getPublicEntries(), $currentNamespaceAccess);
    }

    //todo: Use MatchedResultInterface
    private function isInPrivateConfiguredEntries(Entry $currentNamespaceAccess, MatchCollectionInterface $macher): bool
    {
        return $macher->evaluate($this->globalConfig->getPrivateEntries(), $currentNamespaceAccess);
    }

    private function isInConfiguredComposerPsr4Namespaces(Entry $val, MatchCollectionInterface $macher): bool
    {
        $val = $this->stripFirstSlash($val);

        return $this->metadataLoader->getCollectComposerNamespace()->booleanSearch($macher, $val);
    }
}
