<?php

declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Result\ResultParser;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultAnalyser;
use NamespaceProtector\Parser\ParserInterface;
use NamespaceProtector\Result\ResultCollected;
use NamespaceProtector\Result\ResultAnalyserInterface;
use NamespaceProtector\Result\ResultCollectedReadable;
use NamespaceProtector\Result\Factory\CollectedFactory;
use NamespaceProtector\Result\ResultProcessedFileInterface;

final class Analyser
{
    /** @var ParserInterface[]  */
    private $parserList;

    /** @var CollectedFactory */
    private $collectedFactory;

    public function __construct(
        CollectedFactory $collectedFactory,
        ParserInterface ...$parserList
    ) {
        $this->parserList = $parserList;
        $this->collectedFactory = $collectedFactory;
    }

    public function execute(PathInterface $filePath): ResultAnalyserInterface
    {
        $collection = $this->collectedFactory->createEmptyChangeableProcessedFile();
        $cumulativeParserResult = new ResultParser($collection);

        foreach ($this->parserList as $currentParser) {
            $resultOfcurrentParsedFile = $currentParser->parseFile($filePath);

            $cumulativeParserResult->append($resultOfcurrentParsedFile);
        }

        return $this->getResult($cumulativeParserResult);
    }

    private function getResult(ResultParser $resultParser): ResultAnalyserInterface
    {
        return new ResultAnalyser($this->convertReadOnlyCollectionToEditableCollection($resultParser->getResultCollectionReadable()));
    }

    /**
     *
     * @param ResultCollectedReadable<ResultProcessedFileInterface> $resultCollectedReadable
     * @return ResultCollected<ResultProcessedFileInterface>
     */
    private function convertReadOnlyCollectionToEditableCollection(ResultCollectedReadable $resultCollectedReadable): ResultCollected
    {
        $collection = $this->collectedFactory->createEmptyChangeableProcessedFile();

        foreach ($resultCollectedReadable as $item) {
            $collection->addResult($item);
        }

        return $collection;
    }
}
