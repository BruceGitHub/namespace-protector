<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

interface ResultProcessedFileInterface extends ResultInterface
{
    public function getFileName(): String;

    /** @return array<ErrorResult> */
    public function getConflicts(): array;
}
