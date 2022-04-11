<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Entry\Entry;
use Psr\SimpleCache\CacheInterface;
use NamespaceProtector\Config\Config;
use NamespaceProtector\Rule\FalsePositive;
use NamespaceProtector\Rule\IsWithPrivateNamespace;
use NamespaceProtector\EnvironmentDataLoaderInterface;
use NamespaceProtector\Rule\IsInConfigureComposerPsr4;
use NamespaceProtector\Rule\IsConfiguredInThirdPartyApp;
use NamespaceProtector\Rule\IsInPrivateConfiguredEntries;
use NamespaceProtector\Parser\Node\Event\EventProcessNodeInterface;

final class ProcessUseStatement
{
    public function __construct(
        private EnvironmentDataLoaderInterface $metadataLoader,
        private Config $appConfig,
        private CacheInterface $cache
    ) {
    }

    public function __invoke(EventProcessNodeInterface $event): void
    {
        $isConfiguredComposerPsr4 = new IsInConfigureComposerPsr4($this->metadataLoader);
        $rules = [
            new FalsePositive($this->metadataLoader),
            new IsWithPrivateNamespace($this->appConfig, $isConfiguredComposerPsr4),
            $isConfiguredComposerPsr4,
            new IsInPrivateConfiguredEntries($this->appConfig),
            new IsConfiguredInThirdPartyApp($this->metadataLoader, $this->appConfig),
        ];

        foreach ($rules as $rule) {
            if ($rule->apply(new Entry(StringVo::fromValue($event->getNodeName()->toValue())), $event)) {
                return;
            }
        }
    }
}
