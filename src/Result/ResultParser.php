<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultParser implements ResultParserInterface
{
    /** @var ResultCollected<ResultProcessedFileInterface> */
    private $collectedResultParser;

    /**
     * @param ResultCollected<ResultProcessedFileInterface> $collectedResultParser
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
