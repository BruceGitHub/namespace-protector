<?php
namespace NamespaceProtector;

use NamespaceProtector\Analyser;
use NamespaceProtector\Scanner\ComposerJson;
use NamespaceProtector\Scanner\FileSystemScanner;

class NamespaceProtectorProcessor
{
    private const NAMESPACE_PROTECTOR_CACHE = 'namespace-protector-cache';

    private $config;
    private $composerJson;
    private $fileSystemScanner;
    private $analyser;
    private $environmentDataLoader;
    private $context = [];

    public function __construct(
        ComposerJson $composerJson,
        FileSystemScanner $fileSystemScanner,
        Analyser $analyser,
        EnvironmentDataLoader $environmentDataLoader
    ) {
        $this->composerJson = $composerJson;
        $this->fileSystemScanner = $fileSystemScanner;
        $this->analyser = $analyser;
        $this->environmentDataLoader = $environmentDataLoader;
    }

    public function load(): void
    {
        $this->composerJson->load();
        $this->fileSystemScanner->load();
        $this->environmentDataLoader->load();
    }

    public function getFilesLoaded(): int
    {
        return \count($this->fileSystemScanner->getFileLoaded());
    }

    public function getCountErrors(): int
    {
        return $this->analyser->getCountErrors();
    }

    public function totalSymbolsLoaded(): int
    {
        return
            ($this->environmentDataLoader->getCollectBaseClasses()->count()) +
            ($this->environmentDataLoader->getCollectBaseInterfaces()->count()) +
            ($this->environmentDataLoader->getCollectBaseFunctions()->count()) +
            ($this->environmentDataLoader->getCollectBaseConstants()->count());
    }

    public function process(): bool
    {
        $this->processEntries($this->fileSystemScanner, $this->analyser);

        if ($this->analyser->withError()) {
            return false;
        }

        return true;
    }

    private function processEntries(FileSystemScanner $fileSystemScanner, Analyser $analyser): void
    {
        foreach ($fileSystemScanner->getFileLoaded() as $file) {
            $analyser->execute($file);
        }
    }
}