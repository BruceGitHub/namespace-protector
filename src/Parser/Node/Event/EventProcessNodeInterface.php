<?php declare(strict_types=1);

namespace NamespaceProtector\Parser\Node\Event;

use NamespaceProtector\Event\EventInterface;

interface EventProcessNodeInterface extends EventInterface
{
    public function foundError(): void;

    public function withError(): bool;

    public function getNodeName(): string;
}
