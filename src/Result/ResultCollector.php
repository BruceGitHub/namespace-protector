<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

final class ResultCollector implements ResultCollectorInterface
{
    /** @var array<ResultInterface>  */
    private $listResult;

    /**
     * @param array<ResultInterface> $result
     */
    public function __construct(array $result = [])
    {
        $this->listResult = $result;
    }

    public function addResult(ResultInterface $result): void
    {
        $this->listResult[] = $result;
    }

    public function count(): int
    {
        return \count($this->listResult);
    }

    /** @return  array<ResultInterface>  */
    public function get(): array
    {
        return $this->listResult;
    }

    public function emptyResult(): void
    {
        $this->listResult = [];
    }
}
