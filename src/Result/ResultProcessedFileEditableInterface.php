<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

interface ResultProcessedFileEditableInterface extends ResultProcessedFileInterface
{
    public function get(): String;

    /** @return array<ResultInterface> */
    public function getConflicts(): array;

    public function addConflic(ResultInterface $conflic): void;
}
