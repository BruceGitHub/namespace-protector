<?php

namespace NamespaceProtector;

final class MetadataLoader {

    private $collectBaseClasses = []; 
    private $collectBaseInterfaces = []; 
    private $collectBaseFunctions = [];
    private $collectBaseConstants = [];

    public function __construct()
    {
    }

    public function load(): void
    {
        $this->collectBaseClasses = \get_declared_classes();
        $this->collectBaseInterfaces = \get_declared_interfaces();
        $this->collectBaseFunctions = \get_defined_functions()['internal'];
        $this->collectBaseConstants = \get_defined_constants();


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
