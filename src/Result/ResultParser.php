<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultParser implements ResultParserInterface
{
    /** @var ResultCollected<\NamespaceProtector\Result\ResultProcessedFileInterface> */
    private ResultCollected $collectedResultParser;

    /**
     * @param \NamespaceProtector\Result\ResultCollected<\NamespaceProtector\Result\ResultProcessedFileInterface> $collectedResultParser
     */
    public function __construct(ResultCollected $collectedResultParser)
    {
        $this->collectedResultParser = $collectedResultParser;
    }

    public function getResultCollectionReadable(): ResultCollectedReadable
    {
        return new ResultCollectedReadable($this->collectedResultParser);
    }

    public function append(ResultParserInterface $toAppend): void
    {
        foreach ($toAppend->getResultCollectionReadable() as $item) {
            $this->collectedResultParser->addResult($item);
        }
    }
}
