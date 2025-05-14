<?php

namespace Juggernaut\OpenEMR\Modules\GoogleCalendar;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Bootstrap
{

    /**
     * @var EventDispatcherInterface The object responsible for sending and subscribing to events through the OpenEMR system
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher
     * @param mixed $kernel
     */
    public function __construct(EventDispatcher $eventDispatcher, mixed $kernel)
    {
    }

    public function subscribeToEvents()
    {
    }
}
