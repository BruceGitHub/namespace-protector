<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

use MinimalVo\BaseValueObject\StringVo;

interface ResultProcessedFileInterface extends ResultInterface
{
    public function getFileName(): StringVo;

    /** @return array<ErrorResult> */
    public function getConflicts(): array;
}
