<?php

namespace Juggernaut\Module\PatientCheckin;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Bootstrap
{
    /**
     * @var EventDispatcherInterface The object responsible for sending and subscribing to events through the OpenEMR system
     */
    private $eventDispatcher;

    private $kernel;

    public function __construct($eventDispatcher, $kernel)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->kernel = $kernel;
    }

    public function subscribeToEvents()
    {
    }

    public function onModuleLoad($event)
    {
    }
}
