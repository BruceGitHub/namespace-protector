<?php

namespace NamespaceProtector;

use NamespaceProtector\Scanner\ComposerJson;

final class EnvironmentDataLoader
{

    /** @var array<string>  */
    private $collectBaseClasses = [];

    /** @var array<string>  */
    private $collectBaseInterfaces = [];

    /** @var array<string>  */
    private $collectBaseFunctions = [];

    /** @var array<string>  */
    private $collectBaseConstants = [];

    /** @var array<string>  */
    private $collectVendorNamespace = [];

    /** @var ComposerJson  */
    private $composerJson;

    public function __construct(ComposerJson $composerJson)
    {
        $this->composerJson = $composerJson;
    }

    public function load(): void
    {
        $this->collectBaseClasses = \get_declared_classes();
        $this->collectBaseInterfaces = \get_declared_interfaces();
        $this->collectBaseFunctions = \get_defined_functions()['internal'];
        $this->collectBaseConstants = \get_defined_constants();
        $this->collectVendorNamespace = $this->composerJson->getPsr4Ns();
    }

    /**
     * @return array<string>
     */
    public function getCollectBaseClasses(): array
    {
        return $this->collectBaseClasses;
    }

    /**
     * @return array<string>
     */
    public function getCollectBaseInterfaces(): array
    {
        return $this->collectBaseInterfaces;
    }

    /**
     * @return array<string>
     */
    public function getCollectBaseFunctions(): array
    {
        return $this->collectBaseFunctions;
    }

    /**
     * @return array<string>
     */
    public function getCollectBaseConstants(): array
    {
        return $this->collectBaseFunctions;
    }

    /**
     * @return array<string>
     */
    public function getCollectVendorNamespace(): array
    {
        return $this->collectVendorNamespace;
    }

    /**
     * @param  array<string> $array
     */
    public static function valueExist(array $array, string $value): bool
    {
        if (\in_array($value, $array, true)) {
            return false;
        }

        return true;
    }

    /**
     * @param  array<string> $array
     */
    public static function keyExist(array $array, string $value): bool
    {
        if (\array_key_exists($value, $array)) {
            return false;
        }

        return true;
    }
}
