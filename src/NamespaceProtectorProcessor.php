<?php

declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Scanner\ComposerJson;
use NamespaceProtector\Result\ResultAnalyser;
use NamespaceProtector\Result\ResultProcessor;
use NamespaceProtector\Scanner\FileSystemScanner;
use NamespaceProtector\Result\ResultAnalyserInterface;
use NamespaceProtector\Result\ResultCollectedReadable;
use NamespaceProtector\Result\Factory\CollectedFactory;
use NamespaceProtector\Result\ResultProcessorInterface;
use NamespaceProtector\Result\ResultProcessedFileInterface;

final class NamespaceProtectorProcessor
{
    /** @var ComposerJson */
    private $composerJson;

    /** @var FileSystemScanner */
    private $fileSystemScanner;

    /** @var Analyser */
    private $analyser;

    /** @var EnvironmentDataLoaderInterface */
    private $environmentDataLoader;

    /** @var CollectedFactory */
    private $collectedFactory;

    public function __construct(
        ComposerJson $composerJson,
        FileSystemScanner $fileSystemScanner,
        Analyser $analyser,
        EnvironmentDataLoader $environmentDataLoader,
        CollectedFactory $collectedFactory
    ) {
        $this->composerJson = $composerJson;
        $this->fileSystemScanner = $fileSystemScanner;
        $this->analyser = $analyser;
        $this->environmentDataLoader = $environmentDataLoader;
        $this->collectedFactory = $collectedFactory;
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

        /** @var ResultCollectedReadable<ResultProcessedFileInterface> $resultCollector */
        $resultCollector = $result->getResultCollected();

        return new ResultProcessor($resultCollector);
    }

    private function processEntries(FileSystemScanner $fileSystemScanner, Analyser $analyser): ResultAnalyserInterface
    {
        $collection = $this->collectedFactory->createEmptyChangeableProcessedFile();

        $totalResult = new ResultAnalyser($collection);

        foreach ($fileSystemScanner->getFileLoaded() as $file) {
            $tmp = $analyser->execute($file);
            $totalResult->append($tmp);
        }

        return $totalResult;
    }
}
