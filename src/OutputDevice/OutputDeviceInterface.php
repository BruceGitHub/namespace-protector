<?php 
namespace NamespaceProtector\OutputDevice;

interface OutputDeviceInterface 
{
    public function output(string $value): void;    
}
