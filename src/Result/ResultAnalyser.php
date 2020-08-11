<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultAnalyser implements ResultAnalyserInterface
{
    /** @var ResultCollectorReadable */
    private $resultCollectorReadable;

    public function __construct(ResultCollectorReadable $resultCollectorReadable)
    {
        $this->resultCollectorReadable = $resultCollectorReadable;
    }

    public function append(ResultAnalyserInterface $toAppendInstance): ResultAnalyserInterface
    {
        $collector = new ResultCollector();
        foreach ($this->getResultCollector() as $item) {
            $collector->addResult($item);
        }

        foreach ($toAppendInstance->getResultCollector() as $item) {
            $collector->addResult($item);
        }

        return new self(new ResultCollectorReadable($collector));
    }

    public function withResults(): bool
    {
        return ($this->count()) >= 0 ? true : false;
    }

    public function getResultCollector(): ResultCollectorReadable
    {
        return $this->resultCollectorReadable;
    }

    public function count(): int
    {
        return \count($this->resultCollectorReadable);
    }
}
