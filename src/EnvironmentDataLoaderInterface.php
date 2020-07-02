<?php

namespace NamespaceProtector;

use NamespaceProtector\Db\DbKeyValue;

interface EnvironmentDataLoaderInterface
{
    public function getCollectBaseConstants(): DbKeyValue;

    public function getCollectBaseInterfaces(): DbKeyValue;

    public function getCollectBaseClasses(): DbKeyValue;

    public function getCollectBaseFunctions(): DbKeyValue;

    public function getCollectComposerNamespace(): DbKeyValue;

    public function load(): void;
}
