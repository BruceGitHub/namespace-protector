<?php declare(strict_types=1);

namespace NamespaceProtector\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

final class EventDispatcher implements EventDispatcherInterface
{
    private ListenerProviderInterface $listenerProvider;

    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
    }

    public function dispatch(object $event)
    {
        /**
         * @var callable listener
        */
        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
            // if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            //     break;
            // }

            $listener($event);
        }
        return $event;
    }
}
