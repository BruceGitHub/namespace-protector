<?php declare(strict_types=1);

namespace NamespaceProtector\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /** @var ListenerProviderInterface*/
    private $listenerProvider;

    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
    }

    public function dispatch(object $event)
    {
        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
            // if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            //     break;
            // }

            $listener($event);
        }
        return $event;
    }
}
