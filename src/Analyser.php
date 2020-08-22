<?php

declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Result\ResultParser;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultAnalyser;
use NamespaceProtector\Parser\ParserInterface;
use NamespaceProtector\Result\ResultAnalyserInterface;
use NamespaceProtector\Result\ResultCollectedReadable;

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
        $cumulativeResultFromParser = new ResultParser();
        foreach ($this->parserList as $currentParser) {
            $resultOfcurrentParsedFile = $currentParser->parseFile($filePath);

            $cumulativeResultFromParser->append($resultOfcurrentParsedFile);
        }

        return $this->getResult($cumulativeResultFromParser);
    }

    private function getResult(ResultParser $resultParser): ResultAnalyserInterface
    {
        return new ResultAnalyser(
            new ResultCollectedReadable($resultParser->getResultCollectionReadable())
        );
    }
}
