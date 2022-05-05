<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use MinimalVo\BaseValueObject\IntegerVo;
use MinimalVo\BaseValueObject\StringVo;

final class Result implements ResultInterface
{
    public function __construct(
        private StringVo $value,
        private IntegerVo $type = new IntegerVo(0)
    )
    {
    }

    public function get(): StringVo
    {
        return $this->value;
    }

    public function getType(): IntegerVo
    {
        return $this->type;
    }
}
