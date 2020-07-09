<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Config\Config;
use NamespaceProtector\Db\BooleanMatchKey;
use NamespaceProtector\Db\BooleanMatchPos;
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

    public function __construct(
        EnvironmentDataLoaderInterface $metadataLoader,
        Config $configGlobal
    ) {
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

        if (true === $this->isInConfiguredComposerPsr4Namespaces($val)) {
            return;
        }

        if (true === $this->isInPrivateConfiguredEntries($val)) {
            $event->foundError();
            return;
        }
    }

    private function withModeVendorPrivate(Entry $currentNamespaceAccess, EventProcessNodeInterface $event): void
    {
        if ($this->isInPublicConfiguredEntries($currentNamespaceAccess)) {
            return;
        }

        if (true === $this->isInConfiguredComposerPsr4Namespaces($currentNamespaceAccess)) {
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

    private function isInPublicConfiguredEntries(Entry $currentNamespaceAccess): bool
    {
        foreach ($this->globalConfig->getPublicEntries() as $publicEntry) {
            $publicEntry = \strtolower($publicEntry);
            $current = \strtolower($currentNamespaceAccess->get());
            if (strpos($current, $publicEntry) !== false) {
                return true;
            }
        }

        return false;
    }

    private function isInPrivateConfiguredEntries(Entry $currentNamespaceAccess): bool
    {
        foreach ($this->globalConfig->getPrivateEntries() as $privateEntry) {
            $privateEntry = \strtolower($privateEntry);
            $current = \strtolower($currentNamespaceAccess->get());
            if (strpos($current, $privateEntry) !== false) {
                return true;
            }
        }
        return false;
    }

    private function isInConfiguredComposerPsr4Namespaces(Entry $val): bool
    {
        $val = $this->stripFirstSlash($val);

        if ($this->metadataLoader
            ->getCollectComposerNamespace()
            ->booleanSearch(new BooleanMatchPos(), $val)
        ) {
            return true;
        }

        return false;
    }
}
