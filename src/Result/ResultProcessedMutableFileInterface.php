<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use MinimalVo\BaseValueObject\StringVo;

interface ResultProcessedMutableFileInterface extends ResultProcessedFileInterface
{
    public function getFileName(): StringVo;

    /** @return array<ErrorResult> */
    public function getConflicts(): array;

    public function getReadOnlyProcessedFile(): ResultProcessedFileReadOnly;

    public function addConflic(ErrorResult $conflic): void;
}
