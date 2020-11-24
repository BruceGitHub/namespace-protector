<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

final class ResultProcessedFileReadOnly implements ResultProcessedFileInterface
{
    private string $file;

    /** @var array<ErrorResult> */
    private array $conflicts = [];

    /** @param array<ErrorResult> $conflicts*/
    public function __construct(string $file, array $conflicts)
    {
        $this->file = $file;
        $this->conflicts = $conflicts;
    }

    public function getFileName(): String
    {
        return $this->file;
    }

    /** @return array<ErrorResult> */
    public function getConflicts(): array
    {
        return $this->conflicts;
    }
}
