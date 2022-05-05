<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use MinimalVo\BaseValueObject\IntegerVo;
use MinimalVo\BaseValueObject\StringVo;

final class ErrorResult implements ResultInterface
{
    public function __construct(private IntegerVo $line, private StringVo $use, private IntegerVo $type)
    {
    }

    public function get(): StringVo
    {
        return $this->use;
    }

    public function getType(): IntegerVo
    {
        return $this->type;
    }

    public function getUse(): string
    {
        return $this->use->toValue();
    }

    public function getLine(): IntegerVo
    {
        return $this->line;
    }
}
