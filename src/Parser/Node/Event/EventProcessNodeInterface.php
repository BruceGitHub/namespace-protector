<?php declare(strict_types=1);

namespace NamespaceProtector\Parser\Node\Event;

use NamespaceProtector\Event\EventInterface;

interface EventProcessNodeInterface extends EventInterface
{
    public function foundError(string $additionalInfo = ''): void;

    public function withError(): bool;

    public function getNodeName(): string;

    public function getAdditionalInformation(): string;
}
