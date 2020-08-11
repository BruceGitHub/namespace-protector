<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

use Iterator;
use Countable;

/**
 * @implements ResultCollectorInterface<ResultInterface>
 */
final class ResultCollectorReadable implements Countable, ResultCollectorInterface
{
    /** @var ResultCollectorInterface<ResultInterface> */
    private $resultCollector;

    /**
     * @param ResultCollectorInterface<ResultInterface> $resultCollector
     */
    public function __construct(ResultCollectorInterface $resultCollector)
    {
        $this->resultCollector = $resultCollector;
    }

    public function count(): int
    {
        return \count($this->resultCollector);
    }

    /**
     * @return Iterator<ResultInterface>
     */
    public function getIterator(): Iterator
    {
        return $this->resultCollector->getIterator();
    }
}
