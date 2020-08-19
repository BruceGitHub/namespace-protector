<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

final class ResultProcessedFileEmpty implements ResultProcessedFileInterface
{
    public function get(): String
    {
        return '';
    }

    /** @return array<ResultInterface> */
    public function getConflicts(): array
    {
        return [];
    }
}
