<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultParser implements ResultParserInterface
{
    /** @var ResultCollectorReadable */
    private $resultCollectorReadable;

    public function __construct(ResultCollectorReadable $resultCollectorReadable)
    {
        $this->resultCollectorReadable = $resultCollectorReadable;
    }

    public function getResultCollectionReadable(): ResultCollectorReadable
    {
        return $this->resultCollectorReadable;
    }

    public function append(ResultParserInterface $toAppendInstance): ResultParserInterface
    {
        $collector = new ResultCollector();
        foreach ($this->getResultCollectionReadable()->get() as $item) {
            $collector->addResult($item);
        }

        foreach ($toAppendInstance->getResultCollectionReadable()->get() as $item) {
            $collector->addResult($item);
        }
        return new self(new ResultCollectorReadable($collector));
    }
}
