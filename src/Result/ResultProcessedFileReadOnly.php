<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

final class ResultProcessedFileReadOnly implements ResultProcessedFileInterface
{
    /** @param array<ErrorResult> $conflicts*/
    public function __construct(private string $file, private array $conflicts)
    {
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
