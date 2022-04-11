<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use MinimalVo\BaseValueObject\StringVo;

final class ErrorResult implements ResultInterface
{
    public function __construct(private int $line, private StringVo $use, private int $type)
    {
    }

    public function get(): StringVo
    {
        return $this->use;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getUse(): string
    {
        return $this->use->toValue();
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
