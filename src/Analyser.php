<?php declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Result\ResultParser;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultAnalyser;
use NamespaceProtector\Parser\ParserInterface;
use NamespaceProtector\Result\ResultAnalyserInterface;
use NamespaceProtector\Result\ResultCollector;
use NamespaceProtector\Result\ResultCollectorReadable;

final class Analyser
{
    /** @var ParserInterface[]  */
    private $listParser;

    public function __construct(ParserInterface ...$listParser)
    {
        $this->listParser = $listParser;
    }

    public function execute(PathInterface $filePath): ResultAnalyserInterface
    {
        $totalParserResult = new ResultParser(new ResultCollectorReadable(new ResultCollector()));
        foreach ($this->listParser as $currentParser) {
            $result = $currentParser->parseFile($filePath);

            $totalParserResult = $totalParserResult->append($result);
        }

        return new ResultAnalyser($totalParserResult->getResultCollectionReadable());
    }
}
