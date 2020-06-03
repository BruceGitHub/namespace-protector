<?php

namespace App;

use App\Common\PathInterface;

final class Config {

    private $pathStart;
    private $namespaceToValidate;

    public function __construct(
        PathInterface $pathStart,
        array $namespaceToValidate
    )
    {
        $this->pathStart = $pathStart;
        $this->namespaceToValidate = $namespaceToValidate; 
    }

    public function getStartPath(): PathInterface
    {
        return $this->pathStart;
    }

    public function getNamespaceToValidate(): array 
    {
        return $this->namespaceToValidate; 
    }

    public function print(): string 
    {
        //todo: automatic dump config 
        return 
            'Dump config:'.PHP_EOL.
            '--> $pathStart: '.$this->pathStart->get().PHP_EOL.
            '--> $namespaceToValidate: '.
                    array_walk(
                $this->getNamespaceToValidate(),
                function ($value, $key) {
                    echo $key; 
                },
            );
            ;
        ;
    }
}