<?php declare(strict_types=1);

namespace NamespaceProtector;

use Closure;
use ReflectionClass;
use NamespaceProtector\Db\DbKeyValue;
use NamespaceProtector\Scanner\ComposerJson;

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

    /** @var ComposerJson */
    private $composerJson;

    public function __construct(ComposerJson $composerJson)
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
    }

    public function load(): void
    {
        $fetchValue = function ($key, $value) {return $value; };
        $fetchKey = function ($key, $value) {return $key; };

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
    }

    /**
     * @return DbKeyValue
     */
    public function getCollectBaseClasses(): DbKeyValue
    {
        return $this->collectBaseClasses;
    }

    /**
     * @return DbKeyValue
     */
    public function getCollectBaseInterfaces(): DbKeyValue
    {
        return $this->collectBaseInterfaces;
    }

    /**
     * @return DbKeyValue
     */
    public function getCollectBaseFunctions(): DbKeyValue
    {
        return $this->collectBaseFunctions;
    }

    /**
     * @return DbKeyValue
     */
    public function getCollectBaseConstants(): DbKeyValue
    {
        return $this->collectBaseConstants;
    }

    /**
     * @return DbKeyValue
     */
    public function getCollectComposerNamespace(): DbKeyValue
    {
        return $this->collectComposerNamespace;
    }

    /**
     * @param array<string> $collections
     */
    private function fillFromArray(array $collections, Closure $fetchValue): DbKeyValue
    {
        $db = new DbKeyValue();
        foreach ($collections as $key => $value) {
            $checkValue = $fetchValue($key, $value);
            $pos = \strpos($checkValue, self::NAMESPACE_PROJECT);

            if ($pos === false) {
                $db->add((string)$key, (string)$value);
            }
        }

        return $db;
    }
}
