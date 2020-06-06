<?php

namespace NamespaceProtector;

use NamespaceProtector\Db\DbKeyValue;
use NamespaceProtector\Scanner\ComposerJson;

final class EnvironmentDataLoader
{

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
        $this->collectBaseClasses = new DbKeyValue();
        $this->collectBaseInterfaces = new DbKeyValue();
        $this->collectBaseFunctions = new DbKeyValue();
        $this->collectComposerNamespace = new DbKeyValue();
    }

    public function load(): void
    {
        $this->collectBaseClasses = $this->fillFromArrayKeyValue(\get_declared_classes());
        $this->collectBaseInterfaces = $this->fillFromArrayKeyValue(\get_declared_interfaces());
        $this->collectBaseFunctions = $this->fillFromArrayKeyValue(\get_defined_functions()['internal']);
        $this->collectBaseConstants = $this->fillFromArrayKeyValue(\get_defined_constants());
        $this->collectComposerNamespace = $this->fillFromArrayKeyValue($this->composerJson->getPsr4Ns());
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
    private function fillFromArrayKeyValue(array $collections): DbKeyValue
    {
        $db = new DbKeyValue();
        foreach ($collections as $key => $value) {
            $db->add((string)$key, (string)$value);
        }

        return $db;
    }
}
