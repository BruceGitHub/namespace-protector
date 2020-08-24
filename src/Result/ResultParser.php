<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultParser implements ResultParserInterface
{
    /** @var ResultCollected<ResultProcessedFileInterface> */
    private $collectionResultProcessor;

    /**
     * @param ResultCollected<ResultProcessedFileInterface> $collectionResultProcessor
     */
    public function __construct(ResultCollected $collectionResultProcessor)
    {
        $this->collectionResultProcessor = $collectionResultProcessor;
    }

    public function getResultCollectionReadable(): ResultCollectedReadable
    {
        return new ResultCollectedReadable($this->collectionResultProcessor);
    }

    public function append(ResultParserInterface $toAppendInstance): void
    {
        foreach ($toAppendInstance->getResultCollectionReadable() as $item) {
            $this->collectionResultProcessor->addResult($item);
        }
    }
}
