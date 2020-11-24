<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

final class ErrorResult implements ResultInterface
{
    private int $type;

    private int $line;

    private string $use;

    public function __construct(int $line, string $use, int $type)
    {
        $this->line = $line;
        $this->use = $use;
        $this->type = $type;
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
