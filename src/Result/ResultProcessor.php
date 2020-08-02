<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

class ResultProcessor implements ResultProcessorInterface
{
    /** @var array<string> */
    private $lines;

    /** @var ResultCollectorReadable */
    private $resultCollectorReadable;

    /**
     * @param array<string> $lines
     */
    public function __construct(array $lines, ResultCollectorReadable $resultCollectorReadable)
    {
        $this->lines = $lines;
        $this->resultCollectorReadable = $resultCollectorReadable;
    }

    /**
     * @inheritDoc
     */
    public function getOutputLines(): array
    {
        return $this->lines;
    }

    public function getResultCollectionReadable(): ResultCollectorReadable
    {
        return $this->resultCollectorReadable;
    }
}
