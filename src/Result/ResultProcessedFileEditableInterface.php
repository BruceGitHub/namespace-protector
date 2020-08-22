<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

interface ResultProcessedFileEditableInterface extends ResultProcessedFileInterface
{
    public function getFileName(): String;

    /** @return array<ErrorResult> */
    public function getConflicts(): array;

    public function addConflic(ErrorResult $conflic): void;
}
