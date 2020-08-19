<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

interface ResultProcessedFileInterface extends ResultInterface
{
    public function get(): String;

    /** @return array<ResultInterface> */
    public function getConflicts(): array;
}
