<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

final class ResultProcessedMutableFile implements ResultProcessedMutableFileInterface
{
    private string $file;

    /** @var array<ErrorResult> */
    private array$conflicts = [];

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function getFileName(): String
    {
        return $this->file;
    }

    public function addConflic(ErrorResult $conflic): void
    {
        $this->conflicts[] = $conflic;
    }

    /** @return array<ErrorResult> */
    public function getConflicts(): array
    {
        return $this->conflicts;
    }

    public function getReadOnlyProcessedFile(): ResultProcessedFileReadOnly
    {
        $processedFile = new ResultProcessedFileReadOnly($this->file, $this->getConflicts());
        return $processedFile;
    }
}
