<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultAnalyser implements ResultAnalyserInterface
{
    /** @var ResultCollectedReadable */
    private $resultCollectorReadable;

    public function __construct(ResultCollectedReadable $resultCollectorReadable)
    {
        $this->resultCollectorReadable = $resultCollectorReadable;
    }

    public function append(ResultAnalyserInterface $toAppendInstance): ResultAnalyserInterface
    {
        $collected = new ResultCollected();
        foreach ($this->getResultCollected() as $item) {
            $collected->addResult($item);
        }

        foreach ($toAppendInstance->getResultCollected() as $item) {
            $collected->addResult($item);
        }

        return new self(new ResultCollectedReadable($collected));
    }

    public function withResults(): bool
    {
        return ($this->count()) >= 0 ? true : false;
    }

    public function getResultCollected(): ResultCollectedReadable
    {
        return $this->resultCollectorReadable;
    }

    public function count(): int
    {
        return \count($this->resultCollectorReadable);
    }
}
