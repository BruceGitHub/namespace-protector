<?php

namespace NamespaceProtector;

use Composer\Autoload\ClassLoader;
use NamespaceProtector\Common\PathInterface;

final class Config {

    public const MODE_PRIVATE = 'PRIVATE';
    public const MODE_PUBLIC = 'PUBLIC';

    private $pathStart;
    private $privateEntries;
    private $publicEntries;
    private $mode;
    private $classLoader;

    public function __construct(
        ClassLoader $classLoader,
        PathInterface $pathStart,
        array $privateEntries,
        array $publicEntries,
        string $mode = self::MODE_PUBLIC
    )
    {
        $this->pathStart = $pathStart;
        $this->privateEntries = $privateEntries;
        $this->publicEntries = $publicEntries;
        $this->mode = $mode;
        $this->classLoader = $classLoader;
    }

    public function getStartPath(): PathInterface
    {
        return $this->pathStart;
    }

    public function getPrivateEntries(): array
    {
        return $this->privateEntries;
    }

    public function getPublicEntries(): array
    {
        return $this->publicEntries;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getClassLoader(): ClassLoader
    {
        return $this->classLoader;
    }

    public function print(): string 
    {
        //todo: automatic dump config

        $prettyPrintPrivateEntries = $this->populateOutputVarFromArray($this->getPrivateEntries());
        $prettyPrintPublicEntries = $this->populateOutputVarFromArray($this->getPublicEntries());

        return
            'Dump config:'.PHP_EOL.
            '--> Path start: '.$this->pathStart->get().PHP_EOL.
            '--> Mode: '.$this->getMode().PHP_EOL.
            '--> Private entries: '.$prettyPrintPrivateEntries.PHP_EOL.
            '--> Public entries: '.$prettyPrintPublicEntries.PHP_EOL
            ;
    }

    private function populateOutputVarFromArray(array $entries): string
    {
        $prettyPrintNamespaceToValidate = "\n";
        foreach ($entries as $namespace) {
            $prettyPrintNamespaceToValidate .= "-------->" . $namespace . "\n";
        }
        return $prettyPrintNamespaceToValidate;
    }
}
