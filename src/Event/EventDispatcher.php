<?php

declare(strict_types=1);

namespace NamespaceProtector\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

final class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(private ListenerProviderInterface $listenerProvider)
    {
    }

    public function dispatch(object $event)
    {
        $this->listenerProvider->getListenersForEvent($event);
        
        /** @var array<callable> */
        $listeners = $this->listenerProvider->getListenersForEvent($event);

        array_map(
            function (callable $listener) use ($event): void {
                $listener($event);
            },
            $listeners
        );

        return $event;
    }
}
