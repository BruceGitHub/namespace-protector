<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

final class ErrorResult implements ResultInterface
{
    public function __construct(private int $line, private string $use, private int $type)
    {
    }

    public function get(): String
    {
        return $this->use;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getUse(): string
    {
        return $this->use;
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
