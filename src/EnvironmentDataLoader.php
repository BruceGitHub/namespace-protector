<?php

namespace NamespaceProtector;

use NamespaceProtector\Scanner\ComposerJson;

final class EnvironmentDataLoader {

    private $collectBaseClasses = []; 
    private $collectBaseInterfaces = []; 
    private $collectBaseFunctions = [];
    private $collectBaseConstants = [];
    private $collectVendorNamespace = [];
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

    public function getCollectBaseClasses(): array 
    {
        return $this->collectBaseClasses;
    }

    public function getCollectBaseInterfaces(): array 
    {
        return $this->collectBaseInterfaces;
    }

    public function getCollectBaseFunctions(): array 
    {
        return $this->collectBaseFunctions;
    }
    
    public function getCollectBaseConstants(): array 
    {
        return $this->collectBaseFunctions;
    }

    public function getCollectVendorNamespace(): array
    {
        return $this->collectVendorNamespace;
    }

    //helper 
    public static function valueExist(array $array,string $value): bool
    {
        if (\in_array($value,$array ,true)) {
            return false; 
        }

        return true; 
    }

    public static function keyExist(array $array,string $value): bool
    {
        if (\array_key_exists($value,$array)) {
            return false; 
        }

        return true;
    }

}
