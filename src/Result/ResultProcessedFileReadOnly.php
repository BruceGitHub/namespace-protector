<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use MinimalVo\BaseValueObject\StringVo;

final class ResultProcessedFileReadOnly implements ResultProcessedFileInterface
{
    /** @param array<ErrorResult> $conflicts*/
    public function __construct(private StringVo $file, private array $conflicts)
    {
    }

    public function getFileName(): StringVo
    {
        return $this->file;
    }

    /** @return array<ErrorResult> */
    public function getConflicts(): array
    {
        return $this->conflicts;
    }
}
