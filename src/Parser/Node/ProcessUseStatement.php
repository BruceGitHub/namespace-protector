<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Config\Config;
use NamespaceProtector\Rule\FalsePositive;
use NamespaceProtector\Rule\IsWithPrivateNamespace;
use NamespaceProtector\EnvironmentDataLoaderInterface;
use NamespaceProtector\Rule\IsInConfigureComposerPsr4;
use NamespaceProtector\Rule\isInPrivateConfiguredEntries;
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

        $isConfiguredComposerPsr4 = new IsInConfigureComposerPsr4($this->metadataLoader);
        $rules = [
            new FalsePositive($this->metadataLoader),
            new IsWithPrivateNamespace($this->globalConfig, $this->metadataLoader, $isConfiguredComposerPsr4),
            $isConfiguredComposerPsr4,
            new isInPrivateConfiguredEntries($this->globalConfig),
        ];

        foreach ($rules as $rule) {
            if ($rule->apply($val, $event)) {
                return;
            }
        }
    }
}
