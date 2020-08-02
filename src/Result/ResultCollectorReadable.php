<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

use Countable;

final class ResultCollectorReadable implements Countable, ResultCollectorInterface
{
    /** @var ResultCollectorInterface */
    private $resultCollector;

    public function __construct(ResultCollectorInterface $resultCollector)
    {
        $this->resultCollector = $resultCollector;
    }

    public function count(): int
    {
        return \count($this->resultCollector);
    }

    /** @return  array<ResultInterface>  */
    public function get(): iterable
    {
        return $this->resultCollector->get();
    }
}
