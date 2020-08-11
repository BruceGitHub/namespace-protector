<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultParser implements ResultParserInterface
{
    /** @var ResultCollectedReadable<ResultProcessorInterface> */
    private $resultCollectorReadableParser;

    /**
     * @param ResultCollectedReadable<ResultProcessorInterface> $resultCollectorReadableParser
     */
    public function __construct(ResultCollectedReadable $resultCollectorReadableParser = null)
    {
        if (null === $resultCollectorReadableParser) {
            /** @var ResultCollectedReadable<ResultProcessorInterface> $resultCollectorReadableParser */
            $resultCollectorReadableParser = new ResultCollectedReadable(new ResultCollected());
            $this->resultCollectorReadableParser = $resultCollectorReadableParser;
            return;
        }

        $this->resultCollectorReadableParser = $resultCollectorReadableParser;
    }

    public function get(): string
    {
        return '';
    }

    /**
     * @return ResultCollectedReadable<ResultProcessorInterface>
     */
    public function getResultCollectionReadable(): ResultCollectedReadable
    {
        return $this->resultCollectorReadableParser;
    }

    public function append(ResultParserInterface $toAppendInstance): void
    {
        /** @var ResultCollected<ResultProcessorInterface> $collected */
        $collected = new ResultCollected();

        foreach ($this->getResultCollectionReadable() as $item) {
            $collected->addResult($item);
        }

        foreach ($toAppendInstance->getResultCollectionReadable() as $item) {
            $collected->addResult($item);
        }
        /** @var ResultCollectedReadable<ResultProcessorInterface> */
        $resultCollectorReadableParser = new ResultCollectedReadable($collected);
        $this->resultCollectorReadableParser = $resultCollectorReadableParser;
    }
}
