<?php declare(strict_types=1);

namespace NamespaceProtector\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

final class ListenerProvider implements ListenerProviderInterface
{
    /** @var array<array<object>> */
    private array $map = [];

    public function addEventListener(string $eventClassName, object $listenerInstanceEvent): void
    {
        if (!isset($this->map[$eventClassName])) {
            $this->map[$eventClassName] = [];
        }

        $this->map[$eventClassName][] = $listenerInstanceEvent;
    }

    /**
     * @return iterable<object>
     */
    public function getListenersForEvent(object $event): iterable
    {
        $listeners = [];
        $eventClass = \get_class($event);
        foreach ($this->map[$eventClass] as  $listenerInstanceEvent) {
            $listeners[] = $listenerInstanceEvent;
        }

        return $listeners;
    }
}
