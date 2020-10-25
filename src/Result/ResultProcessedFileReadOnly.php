<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

final class ResultProcessedFileReadOnly implements ResultProcessedFileInterface
{
    /** @var string  */
    private $file;

    /** @var array<ErrorResult> */
    private $conflicts = [];

    public function __construct(string $file)
    {
        $this->file = $file;
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