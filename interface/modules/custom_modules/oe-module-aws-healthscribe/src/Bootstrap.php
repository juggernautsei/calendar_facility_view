<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut\Module\HealthScribe;

use OpenEMR\Core\Kernel;
use OpenEMR\Events\Encounter\EncounterMenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Bootstrap
{
    /**
     * @var EventDispatcherInterface The object responsible for sending and subscribing to events through the OpenEMR system
     */
    private $eventDispatcher;
    private ?Kernel $kernel;

    public function __construct(EventDispatcher $dispatcher, ?Kernel $kernel = null)
    {
        if (empty($kernel)) {
            $kernel = new Kernel();
        }

        $this->eventDispatcher = $dispatcher;
        $this->kernel = $kernel;
    }

    public function subscribeToEvents(): void
    {
        $this->registerMenuItems();
    }

    public function registerMenuItems(): void
    {
        /**
         * @var EventDispatcherInterface $eventDispatcher
         * @var array $module
         * @global                       $eventDispatcher @see ModulesApplication::loadCustomModule
         * @global                       $module @see ModulesApplication::loadCustomModule
         */
        $this->eventDispatcher->addListener(EncounterMenuEvent::MENU_RENDER, [$this, 'addHealthScribeEncItem']);
    }

    public function addHealthScribeEncItem(EncounterMenuEvent $event): EncounterMenuEvent
    {
        $menu = $event->getMenuData();
        $menu['AWS'] = [
                'children' => [
                    [
                        'state' => 1,
                        'directory' => 'oe-module-aws-healthscribe',
                        'id' => 41,
                        'unpackaged' => 1,
                        'date' => '2023-03-01 00:00:00',
                        'priority' => 13,
                        'aco_spec' => 'encounters|coding',
                        'LBF' => '',
                        'displayText' => 'Scribe',
                        ]
                ],
        ];
        $event->setMenuData($menu);
        return $event;
    }
}
