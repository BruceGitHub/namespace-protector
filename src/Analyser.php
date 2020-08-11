<?php declare(strict_types=1);

namespace NamespaceProtector;

use NamespaceProtector\Result\ResultParser;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultAnalyser;
use NamespaceProtector\Parser\ParserInterface;
use NamespaceProtector\Result\ResultAnalyserInterface;
use NamespaceProtector\Result\ResultCollectorReadable;

final class Analyser
{
    /** @var ParserInterface[]  */
    private $listParser;

    /** @var ResultParser */
    private $result;

    public function __construct(ParserInterface ...$listParser)
    {
        $this->listParser = $listParser;
    }

    public function execute(PathInterface $filePath): void
    {
        $resultParser = new ResultParser();
        $this->result = $resultParser;
        foreach ($this->listParser as $currentParser) {
            $currentParser->parseFile($filePath);
            $resultOfcurrentParsedFile = $currentParser->getListResult();

            $this->result->append($resultOfcurrentParsedFile);
        }
    }

    public function getResult(): ResultAnalyserInterface
    {
        return new ResultAnalyser(
            new ResultCollectorReadable($this->result->getResultCollectionReadable())
        );
    }
}
