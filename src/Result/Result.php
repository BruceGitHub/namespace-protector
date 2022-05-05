<?php

declare(strict_types=1);

namespace NamespaceProtector\Result;

use MinimalVo\BaseValueObject\IntegerVo;
use MinimalVo\BaseValueObject\StringVo;

final class Result implements ResultInterface
{
    private IntegerVo $type;

    public function __construct(private StringVo $value,int $type = 0)
    {
        $this->type = IntegerVo::fromValue($type);
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
