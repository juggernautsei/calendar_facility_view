<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * All rights reserved
 */

namespace Juggernaut\Module\PatientAlerts;

use OpenEMR\Core\Kernel;
use OpenEMR\Events\Patient\Summary\Card\SectionEvent;
use OpenEMR\Menu\MenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Bootstrap
{
    /**
     * @var EventDispatcherInterface The object responsible for sending and subscribing to events through the OpenEMR system
     */
    private $eventDispatcher;
    public function __construct(EventDispatcher $dispatcher, ?Kernel $kernel = null)
    {
        //global $GLOBALS;

        if (empty($kernel)) {
            $kernel = new Kernel();
        }

        $this->eventDispatcher = $dispatcher;
    }

    public function subscribeToEvents()
    {
        $this->registerModuleMenuItems();
        $this->demographicsHook();
    }

    public function registerModuleMenuItems(): void
    {
        $this->eventDispatcher->addListener(MenuEvent::MENU_UPDATE, [$this, 'addCustomModuleMenuItem']);
    }

    public function demographicsHook(): void
    {
        $this->eventDispatcher->addListener(SectionEvent::EVENT_HANDLE, [$this, 'simplePatientAlert']);
    }

    public function simplePatientAlert(SectionEvent $event): void
    {
        $section = $event->getSection();
        $alert = new AlertsManager();
        $alert = $alert->getSinglePatientAlert($_SESSION['pid']);
        if ($section == 'primary' && !empty($alert)) {
        ?>
        <script>alert("Simple Patient Alert!\n <?php echo $alert; ?>")</script>
<?php
        }
    }
    public function addCustomModuleMenuItem(MenuEvent $event): MenuEvent
    {
        $menu = $event->getMenu();

        $menuItem = new \stdClass();
        $menuItem->requirement = 1;
        $menuItem->target = 'alt1';
        $menuItem->menu_id = 'pat05';
        $menuItem->label = xlt("Simple Patient Alerts");
        $menuItem->url = "/interface/modules/custom_modules/programmable-patient-alerts/public/index.php";
        $menuItem->children = [];
        $menuItem->acl_req = ["patient", "demo"];
        $menuItem->global_req = [];

        foreach ($menu as $item) {
            if ($item->menu_id == 'patimg') {
                $item->children[] = $menuItem;
                break;
            }
        }

        $event->setMenu($menu);

        return $event;
    }

}
