<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

final class ResultCollectorReadable implements ResultCollectorInterface
{
    /** @var ResultCollector */
    private $resultCollector;

    public function __construct(ResultCollector $resultCollector)
    {
        $this->resultCollector = $resultCollector;
    }

    /** @return  array<ResultInterface>  */
    public function get(): iterable
    {
        return $this->resultCollector->get();
    }
}
