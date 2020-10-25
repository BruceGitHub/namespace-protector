<?php declare(strict_types=1);

namespace NamespaceProtector\Rule;

use NamespaceProtector\Entry\Entry;
use NamespaceProtector\Parser\Node\Event\EventProcessNodeInterface;

interface RuleInterface
{
    public function apply(Entry $entry, EventProcessNodeInterface $event): bool;
}
