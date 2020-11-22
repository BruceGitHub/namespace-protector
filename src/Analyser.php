<?php

declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Result\ResultParser;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultAnalyser;
use NamespaceProtector\Parser\ParserInterface;
use NamespaceProtector\Result\ResultAnalyserInterface;
use NamespaceProtector\Result\ResultProcessedFileInterface;
use NamespaceProtector\Result\Factory\CollectionFactoryInterface;

final class Analyser
{
    /** @var ParserInterface[]  */
    private $parserList;

    /** @var CollectionFactoryInterface */
    private $collectedFactory;

    public function __construct(
        CollectionFactoryInterface $collectedFactory,
        ParserInterface ...$parserList
    ) {
        $this->parserList = $parserList;
        $this->collectedFactory = $collectedFactory;
    }

    public function execute(PathInterface $filePath): ResultAnalyserInterface
    {
        $cumulativeParserResult = new ResultParser($this->collectedFactory->createEmptyMutableCollection());

        foreach ($this->parserList as $currentParser) {
            $resultOfcurrentParsedFile = $currentParser->parseFile($filePath);
            $cumulativeParserResult->append($resultOfcurrentParsedFile);
        }

        $resultsParser = new ResultAnalyser($this->collectedFactory);

        /**
         * @var \NamespaceProtector\Result\ResultProcessedFileInterface $currentParserResult
         */
        foreach ($cumulativeParserResult->getResultCollectionReadable() as $currentParserResult) {
            $resultsParser->append($currentParserResult);
        }

        return $resultsParser;
    }
}
