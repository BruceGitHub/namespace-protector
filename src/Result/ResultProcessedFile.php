<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

final class ResultProcessedFile implements ResultInterface
{
    /** @var string  */
    private $file;

    /** @var array<ResultInterface> */
    private $conflicts = [];

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function get(): String
    {
        return $this->file;
    }

    public function add(ResultInterface $conflic): void
    {
        $this->conflicts[] = $conflic;
    }

    /** @return array<ResultInterface> */
    public function getConflicts(): array
    {
        return $this->conflicts;
    }
}
