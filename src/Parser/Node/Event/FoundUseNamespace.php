<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node\Event;

use MinimalVo\BaseValueObject\BooleanVo;
use MinimalVo\BaseValueObject\IntegerVo;
use MinimalVo\BaseValueObject\StringVo;

final class FoundUseNamespace implements EventProcessNodeInterface
{
    private bool $erroDetect = false;

    public function __construct(private IntegerVo $line, private StringVo $nodeName)
    {
    }

    public function getLine(): IntegerVo
    {
        return $this->line;
    }

    public function getNodeName(): StringVo
    {
        return $this->nodeName;
    }

    public function foundError(): void
    {
        $this->erroDetect = true;
    }

    public function withError(): BooleanVo
    {
        return BooleanVo::fromValue($this->erroDetect);
    }
}
