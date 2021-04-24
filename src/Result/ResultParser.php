<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultParser implements ResultParserInterface
{
    /**
     * @param \NamespaceProtector\Result\ResultCollected<\NamespaceProtector\Result\ResultProcessedFileInterface> $collectedResultParser
     */
    public function __construct(private ResultCollected $collectedResultParser)
    {
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
