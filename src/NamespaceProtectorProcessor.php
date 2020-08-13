<?php

declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Config\Config;
use NamespaceProtector\Scanner\ComposerJson;
use NamespaceProtector\Result\ResultAnalyser;
use NamespaceProtector\Result\ResultCollected;
use NamespaceProtector\Result\ResultProcessor;
use NamespaceProtector\Scanner\FileSystemScanner;
use NamespaceProtector\Result\ResultProcessedFile;
use NamespaceProtector\Result\ResultAnalyserInterface;
use NamespaceProtector\Result\ResultCollectedReadable;
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
        return ($this->environmentDataLoader->getCollectBaseClasses()->count()) +
            ($this->environmentDataLoader->getCollectBaseInterfaces()->count()) +
            ($this->environmentDataLoader->getCollectBaseFunctions()->count()) +
            ($this->environmentDataLoader->getCollectBaseConstants()->count());
    }

    public function process(): ResultProcessorInterface
    {
        /** @var ResultAnalyser $result */
        $result = $this->processEntries($this->fileSystemScanner, $this->analyser);

        /** @var ResultCollectedReadable<ResultProcessedFile> $resultCollector */
        $resultCollector = $result->getResultCollected();

        return new ResultProcessor(
            $resultCollector
        );
    }

    private function processEntries(FileSystemScanner $fileSystemScanner, Analyser $analyser): ResultAnalyserInterface
    {
        $totalResult = new ResultAnalyser(new ResultCollectedReadable(new ResultCollected()));

        foreach ($fileSystemScanner->getFileLoaded() as $file) {
            $analyser->execute($file);
            $totalResult = $totalResult->append($analyser->getResult());
        }

        return $totalResult;
    }
}
