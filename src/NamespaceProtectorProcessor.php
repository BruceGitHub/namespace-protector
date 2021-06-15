<?php

declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Common\PathInterface;
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
    public function __construct(
        private ComposerJson $composerJson,
        private FileSystemScanner $filesToAnalyser,
        private Analyser $analyser,
        private EnvironmentDataLoader $environmentDataLoader,
        private CollectionFactoryInterface $collectedFactory
    ) {
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

        \array_map(
            fn (PathInterface $file) => \array_map(
                fn (ResultProcessedFileInterface $processedFile) => $totalResult->append($processedFile),
                \iterator_to_array($analyser->execute($file)->getResultCollected()->getIterator())
            ),
            $filesToAnalyser->getFileLoaded()
        );

        return $totalResult;
    }
}
