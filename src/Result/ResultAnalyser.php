<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use NamespaceProtector\Result\Factory\CollectionFactoryInterface;

class ResultAnalyser implements ResultAnalyserInterface
{
    private ResultCollectedReadable $resultCollection;

    public function __construct(private CollectionFactoryInterface $collectedFactory)
    {
        $this->resultCollection = $collectedFactory->createEmptyReadOnlyCollection();
    }

    public function append(ResultProcessedFileInterface $toAppend): void
    {
        $collection = $this->collectedFactory->createEmptyMutableCollection();

        array_map(
            fn (ResultProcessedFileInterface $item) => $collection->addResult($item),
            iterator_to_array($this->resultCollection->getIterator())
        );

        $collection->addResult($toAppend);

        $this->resultCollection = new ResultCollectedReadable($collection);
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
