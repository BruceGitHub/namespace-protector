<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use NamespaceProtector\Result\Factory\CollectionFactoryInterface;

class ResultAnalyser implements ResultAnalyserInterface
{
    private ResultCollectedReadable $resultCollection;

    private CollectionFactoryInterface $collectedFactory;

    public function __construct(CollectionFactoryInterface $collectedFactory)
    {
        $this->resultCollection = $collectedFactory->createEmptyReadOnlyCollection();

        $this->collectedFactory = $collectedFactory;
    }

    public function append(ResultProcessedFileInterface $toAppend): void
    {
        $collection = $this->collectedFactory->createEmptyMutableCollection();

        /**
         * @var ResultProcessedFileInterface $item
         */
        foreach ($this->resultCollection as $item) {
            $collection->addResult($item);
        }

        $collection->addResult($toAppend);

        $this->resultCollection = new ResultCollectedReadable($collection);
    }

    public function withResults(): bool
    {
        return ($this->count()) >= 0 ? true : false;
    }

    public function getResultCollected(): ResultCollectedReadable
    {
        return $this->resultCollection;
    }

    public function count(): int
    {
        return \count($this->resultCollection);
    }
}
