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
use NamespaceProtector\Result\ResultProcessedFileInterface;

final class Analyser
{
    /** @var ParserInterface[]  */
    private $parserList;

    public function __construct(ParserInterface ...$parserList)
    {
        $this->parserList = $parserList;
    }

    public function execute(PathInterface $filePath): ResultAnalyserInterface
    {
        /** @var ResultCollected<ResultProcessedFileInterface> $collection*/
        $collection = new ResultCollected();
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
        /** @var ResultCollected<ResultProcessedFileInterface> $collection*/
        $collection = new ResultCollected();

        foreach ($resultCollectedReadable as $item) {
            $collection->addResult($item);
        }

        return $collection;
    }
}
