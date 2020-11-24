<?php declare(strict_types=1);

namespace NamespaceProtector\Result;

final class Result implements ResultInterface
{
    private string $value;

    private int $type;

    public function __construct(string $value, int $type = 0)
    {
        $this->value = $value;
        $this->type = $type;
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
