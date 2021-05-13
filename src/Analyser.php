<?php

declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Result\ResultParser;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultAnalyser;
use NamespaceProtector\Parser\ParserInterface;
use NamespaceProtector\Result\ResultAnalyserInterface;
use NamespaceProtector\Result\Factory\CollectionFactoryInterface;

final class Analyser
{
    /** @var ParserInterface[]  */
    private array $parserList;

    private CollectionFactoryInterface $collectedFactory;

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
        $resultsParser = new ResultAnalyser($this->collectedFactory);

        array_map(
            fn ($currentParser) => $cumulativeParserResult->append($currentParser->parseFile($filePath)),
            $this->parserList
        );

        array_map(
            fn ($currentParser) => $resultsParser->append($currentParser),
            iterator_to_array($cumulativeParserResult->getResultCollectionReadable(), true)
        );

        return $resultsParser;
    }
}
