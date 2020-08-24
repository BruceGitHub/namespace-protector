<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultAnalyser implements ResultAnalyserInterface
{
    /**
     * @var ResultCollected<ResultProcessedFileInterface>
     */
    private $resultCollection;

    /**
     * @param ResultCollected<ResultProcessedFileInterface> $resultCollection
     */
    public function __construct(ResultCollected $resultCollection)
    {
        $this->resultCollection = $resultCollection;
    }

    public function append(ResultAnalyserInterface $toAppend): void
    {
        foreach ($toAppend->getResultCollected() as $item) {
            $this->resultCollection->addResult($item);
        }
    }

    public function withResults(): bool
    {
        return ($this->count()) >= 0 ? true : false;
    }

    public function getResultCollected(): ResultCollectedReadable
    {
        return new ResultCollectedReadable($this->resultCollection);
    }

    public function count(): int
    {
        return \count($this->resultCollection);
    }
}
