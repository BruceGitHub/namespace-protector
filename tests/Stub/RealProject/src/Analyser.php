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

    /** @var ResultParser */
    private $cumulativeResultFromParser;

    public function __construct(ParserInterface ...$parserList)
    {
        $this->parserList = $parserList;
    }

    public function execute(PathInterface $filePath): void
    {
        $this->cumulativeResultFromParser = new ResultParser();
        foreach ($this->parserList as $currentParser) {
            $currentParser->parseFile($filePath);
            $resultOfcurrentParsedFile = $currentParser->getListResult();

            $this->cumulativeResultFromParser->append($resultOfcurrentParsedFile);
        }
    }

    public function getResult(): ResultAnalyserInterface
    {
        return new ResultAnalyser(
            new ResultCollectedReadable($this->cumulativeResultFromParser->getResultCollectionReadable())
        );
    }
}
