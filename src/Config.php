<?php

namespace NamespaceProtector;

use NamespaceProtector\Common\PathInterface;

final class Config {

    public const MODE_PRIVATE = 'PRIVATE';
    public const MODE_PUBLIC = 'PUBLIC';

    private $pathStart;
    private $privateEntries;
    private $publicEntries;
    private $mode;

    public function __construct(
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
