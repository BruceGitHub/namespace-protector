<?php declare(strict_types=1);

namespace NamespaceProtector\Parser\Node\Event;

use MinimalVo\BaseValueObject\BooleanVo;
use MinimalVo\BaseValueObject\StringVo;
use NamespaceProtector\Event\EventInterface;

interface EventProcessNodeInterface extends EventInterface
{
    public function foundError(): void;

    public function withError(): BooleanVo;

    public function getNodeName(): StringVo;
}
