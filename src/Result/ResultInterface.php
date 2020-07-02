<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

interface ResultInterface
{
    public function get(): string;

    public function getType(): int;
}
