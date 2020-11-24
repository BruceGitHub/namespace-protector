<?php

declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Scanner\ComposerJson;
use NamespaceProtector\Result\ResultAnalyser;
use NamespaceProtector\Result\ResultProcessor;
use NamespaceProtector\Scanner\FileSystemScanner;
use NamespaceProtector\Result\ResultAnalyserInterface;
use NamespaceProtector\Result\ResultCollectedReadable;
use NamespaceProtector\Result\ResultProcessorInterface;
use NamespaceProtector\Result\ResultProcessedFileInterface;
use NamespaceProtector\Result\Factory\CollectionFactoryInterface;

final class NamespaceProtectorProcessor
{
    private ComposerJson $composerJson;

    private FileSystemScanner $filesToAnalyser;

    private Analyser $analyser;

    private EnvironmentDataLoaderInterface $environmentDataLoader;

    private CollectionFactoryInterface $collectedFactory;

    public function __construct(
        ComposerJson $composerJson,
        FileSystemScanner $filesToAnalyser,
        Analyser $analyser,
        EnvironmentDataLoader $environmentDataLoader,
        CollectionFactoryInterface $collectedFactory
    ) {
        $this->composerJson = $composerJson;
        $this->filesToAnalyser = $filesToAnalyser;
        $this->analyser = $analyser;
        $this->environmentDataLoader = $environmentDataLoader;
        $this->collectedFactory = $collectedFactory;
    }

    public function load(): void
    {
        $this->composerJson->load();
        $this->filesToAnalyser->load();
        $this->environmentDataLoader->load();
    }

    public function getFilesLoaded(): int
    {
        return \count($this->filesToAnalyser->getFileLoaded());
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
        /** @var ResultAnalyser $rAnalyser */
        $rAnalyser = $this->processEntries($this->filesToAnalyser, $this->analyser);

        /** @var ResultCollectedReadable<ResultProcessedFileInterface> $resultCollector */
        $resultCollector = $rAnalyser->getResultCollected();

        return new ResultProcessor($resultCollector);
    }

    private function processEntries(FileSystemScanner $filesToAnalyser, Analyser $analyser): ResultAnalyserInterface
    {
        $totalResult = new ResultAnalyser($this->collectedFactory);

        foreach ($filesToAnalyser->getFileLoaded() as $file) {
            $tmp = $analyser->execute($file);

            /**
             * @var \NamespaceProtector\Result\ResultProcessedFileInterface $processedFile
             */
            foreach ($tmp->getResultCollected() as $processedFile) {
                $totalResult->append($processedFile);
            }
        }

        return $totalResult;
    }
}
