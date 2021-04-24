<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

final class Result implements ResultInterface
{
    public function __construct(private string $value, private int $type = 0)
    {
    }

    public function get(): String
    {
        return $this->value;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
