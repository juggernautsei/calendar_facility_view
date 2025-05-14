<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut\Module\WoundCare;

use OpenEMR\Core\Kernel;
use OpenEMR\Events\Encounter\EncounterMenuEvent;
use OpenEMR\Events\Encounter\LoadEncounterFormFilterEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Bootstrap
{
    /**
     * @var EventDispatcherInterface The object responsible for sending and subscribing to events through the OpenEMR system
     */
    private $eventDispatcher;
    private ?Kernel $kernel;

    const MODULE_NAME = 'WoundCare';
    const MODULE_DIR = 'oe-module-wound-care';
    private $modulePath;

    public function __construct(EventDispatcher $dispatcher, ?Kernel $kernel = null)
    {
        if (empty($kernel)) {
            $kernel = new Kernel();
        }

        $this->eventDispatcher = $dispatcher;
        $this->kernel = $kernel;
        $this->modulePath = $GLOBALS['fileroot'] . '/interface/modules/custom_modules/oe-module-wound-care';
    }

    public function subscribeToEvents(): void
    {
        //$this->registerMenuItems();
        $this->registerFormFilters();
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

    public function registerFormFilters(): void
    {
        $this->eventDispatcher->addListener(LoadEncounterFormFilterEvent::EVENT_NAME, [$this, 'addWoundCareAssistant']);
    }

    public function addHealthScribeEncItem(EncounterMenuEvent $event): EncounterMenuEvent
    {
        $menu = $event->getMenuData();
        $menu['WC AI Agent'] = [
            'children' => [
                [
                    'state' => 1,
                    'directory' => 'oe-module-wound-care',
                    'id' => 41,
                    'unpackaged' => 1,
                    'date' => '2023-03-01 00:00:00',
                    'priority' => 13,
                    'aco_spec' => 'encounters|coding',
                    'LBF' => '',
                    'displayText' => 'Assistant',
                ]
            ],
        ];
        $event->setMenuData($menu);
        return $event;
    }
    public function addWoundCareAssistant(LoadEncounterFormFilterEvent $event): void
    {
        $customForms = [
            'assistant',
        ];

        if (in_array($event->getFormName(), $customForms)) {
            $dir = $this->modulePath . "/" . $event->getFormName() . "/";
            $event->setDir($dir);
        }
    }
}
