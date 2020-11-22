<?php declare(strict_types=1);

namespace NamespaceProtector;

use Closure;
use ReflectionClass;
use NamespaceProtector\Db\DbKeyValue;
use NamespaceProtector\Db\BooleanMatchNameSpace;
use NamespaceProtector\Metadata\VendorNamespace;
use NamespaceProtector\Scanner\ComposerJsonInterface;
use NamespaceProtector\Metadata\VendorNamespaceInterface;

final class EnvironmentDataLoader implements EnvironmentDataLoaderInterface
{
    public const NAMESPACE_PROJECT = 'NamespaceProtector';

    /** @var DbKeyValue */
    private $collectBaseClasses;

    /** @var DbKeyValue */
    private $collectBaseInterfaces;

    /** @var DbKeyValue */
    private $collectBaseFunctions;

    /** @var DbKeyValue */
    private $collectBaseConstants;

    /** @var DbKeyValue */
    private $collectComposerNamespace;

    private VendorNamespaceInterface $vendorNamespaces;

    /** @var ComposerJsonInterface */
    private $composerJson;

    public function __construct(ComposerJsonInterface $composerJson)
    {
        $this->composerJson = $composerJson;
        $this->initializeVars();
    }

    private function initializeVars(): void
    {
        $this->collectBaseClasses = new DbKeyValue();
        $this->collectBaseInterfaces = new DbKeyValue();
        $this->collectBaseFunctions = new DbKeyValue();
        $this->collectBaseConstants = new DbKeyValue();
        $this->collectComposerNamespace = new DbKeyValue();
        $this->vendorNamespaces = new VendorNamespace(new BooleanMatchNameSpace());
    }

    public function load(): void
    {
        /** @var string|int $value */
        $fetchValue = function ($key, $value): string {return (string)$value; };

        $fetchKey = function ($key, $value): string {return (string)$key; };

        $this->collectBaseFunctions = $this->fillFromArray(\get_defined_functions()['internal'], $fetchValue);
        $this->collectBaseInterfaces = $this->fillFromArray(\get_declared_interfaces(), $fetchValue);

        $internalClass = [];
        foreach (get_declared_classes() as $class) {
            $aClass = new ReflectionClass($class);
            if ($aClass->isInternal()) {
                $internalClass[$class] = $aClass->getName();
            }
        }

        $this->collectBaseClasses = $this->fillFromArray($internalClass, $fetchKey);
        $this->collectBaseConstants = $this->fillFromArray(\get_defined_constants(), $fetchKey);
        $this->collectComposerNamespace = $this->fillFromArray($this->composerJson->getPsr4Ns(), $fetchKey);

        $this->vendorNamespaces->load();
    }

    public function getCollectBaseClasses(): DbKeyValue
    {
        return $this->collectBaseClasses;
    }

    public function getCollectBaseInterfaces(): DbKeyValue
    {
        return $this->collectBaseInterfaces;
    }

    public function getCollectBaseFunctions(): DbKeyValue
    {
        return $this->collectBaseFunctions;
    }

    public function getCollectBaseConstants(): DbKeyValue
    {
        return $this->collectBaseConstants;
    }

    public function getCollectComposerNamespace(): DbKeyValue
    {
        return $this->collectComposerNamespace;
    }

    private function fillFromArray(array $collections, Closure $fetchValue): DbKeyValue
    {
        $db = new DbKeyValue();

        /** @var string $value */
        foreach ($collections as $key => $value) {
            /** @var string $checkValue */
            $checkValue = $fetchValue($key, $value);
            $pos = \strpos($checkValue, self::NAMESPACE_PROJECT);

            if ($pos === false) {
                $db->add((string)$key, (string)$value);
            }
        }

        return $db;
    }

    public function vendorNamespaces(): VendorNamespaceInterface
    {
        return $this->vendorNamespaces;
    }
}
