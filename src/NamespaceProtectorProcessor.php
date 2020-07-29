<?php declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Config\Config;
use NamespaceProtector\Scanner\ComposerJson;
use NamespaceProtector\Result\ResultAnalyser;
use NamespaceProtector\Result\ResultProcessor;
use NamespaceProtector\Scanner\FileSystemScanner;
use NamespaceProtector\Result\ResultAnalyserInterface;
use NamespaceProtector\Result\ResultCollector;
use NamespaceProtector\Result\ResultCollectorReadable;
use NamespaceProtector\Result\ResultProcessorInterface;

final class NamespaceProtectorProcessor
{
    /** @var Config */
    private $config;

    /** @var ComposerJson */
    private $composerJson;

    /** @var FileSystemScanner */
    private $fileSystemScanner;

    /** @var Analyser */
    private $analyser;

    /** @var EnvironmentDataLoaderInterface */
    private $environmentDataLoader;

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

    public function totalSymbolsLoaded(): int
    {
        return
            ($this->environmentDataLoader->getCollectBaseClasses()->count()) +
            ($this->environmentDataLoader->getCollectBaseInterfaces()->count()) +
            ($this->environmentDataLoader->getCollectBaseFunctions()->count()) +
            ($this->environmentDataLoader->getCollectBaseConstants()->count());
    }

    public function process(): ResultProcessorInterface
    {
        /** @var ResultAnalyser $result */
        $result = $this->processEntries($this->fileSystemScanner, $this->analyser);
        if ($result->withResults()) {
            return new ResultProcessor(
                ['<fg=red>Total errors: ' . $result->count() . '</>'],
                $result->getResultCollector()
            );
        }

        return new ResultProcessor(['<fg=blue>No output</>'], $result->getResultCollector());
    }

    private function processEntries(FileSystemScanner $fileSystemScanner, Analyser $analyser): ResultAnalyserInterface
    {
        /** @var ResultAnalyserInterface */
        $totalResult = new ResultAnalyser(new ResultCollectorReadable(new ResultCollector()));

        foreach ($fileSystemScanner->getFileLoaded() as $file) {
            $currentResult = $analyser->execute($file);
            $totalResult = $totalResult->append($currentResult);
        }

        return $totalResult;
    }
}
