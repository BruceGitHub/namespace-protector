<?php declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Db\DbKeyValue;
use NamespaceProtector\Metadata\VendorNamespaceInterface;

interface EnvironmentDataLoaderInterface
{
    //todo: remove get become i.e constants, interfaces, classes, etc.
    public function getCollectBaseConstants(): DbKeyValue;

    public function getCollectBaseInterfaces(): DbKeyValue;

    public function getCollectBaseClasses(): DbKeyValue;

    public function getCollectBaseFunctions(): DbKeyValue;

    public function getCollectComposerNamespace(): DbKeyValue;

    // public function vendorNamespaces(): VendorNamespaceInterface;

    public function load(): void;
}
