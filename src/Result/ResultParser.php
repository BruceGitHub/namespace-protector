<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultParser implements ResultParserInterface
{
    /** @var ResultCollectorReadable<ResultProcessorInterface> */
    private $resultCollectorReadableParser;

    /**
     * @param ResultCollectorReadable<ResultProcessorInterface> $resultCollectorReadableParser
     */
    public function __construct(ResultCollectorReadable $resultCollectorReadableParser = null)
    {
        if (null === $resultCollectorReadableParser) {
            /** @var ResultCollectorReadable<ResultProcessorInterface> $resultCollectorReadableParser */
            $resultCollectorReadableParser = new ResultCollectorReadable(new ResultCollector());
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
     * @return ResultCollectorReadable<ResultProcessorInterface>
     */
    public function getResultCollectionReadable(): ResultCollectorReadable
    {
        return $this->resultCollectorReadableParser;
    }

    public function append(ResultParserInterface $toAppendInstance): void
    {
        /** @var ResultCollector<ResultProcessorInterface> $collector */
        $collector = new ResultCollector();

        foreach ($this->getResultCollectionReadable() as $item) {
            $collector->addResult($item);
        }

        foreach ($toAppendInstance->getResultCollectionReadable() as $item) {
            $collector->addResult($item);
        }
        /** @var ResultCollectorReadable<ResultProcessorInterface> */
        $resultCollectorReadableParser = new ResultCollectorReadable($collector);
        $this->resultCollectorReadableParser = $resultCollectorReadableParser;
    }
}
