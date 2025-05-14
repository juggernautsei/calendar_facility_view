<?php

/**
 *
 *  package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 *
 */


use OpenEMR\Menu\MenuEvent;
use OpenEMR\Events\Globals\GlobalsInitializedEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

function oe_module_apptreminder_add_menu_item(MenuEvent $event)
{
    $menu = $event->getMenu();

    $menuItem = new stdClass();
    $menuItem->requirement = 0;
    $menuItem->target = 'mod';
    $menuItem->menu_id = 'dxweb0';
    $menuItem->label = xlt("Automatic Email Reminders");
    $menuItem->url = "/interface/modules/custom_modules/oe-patient-appt-email-reminders/settings.php";
    $menuItem->children = [];
    $menuItem->acl_req = ["patients", "docs"];
    $menuItem->global_req = [];

    foreach ($menu as $item) {
        if ($item->menu_id == 'modimg') {
            $item->children[] = $menuItem;
            break;
        }
    }

    $event->setMenu($menu);

    return $event;
}



/**
 * @var EventDispatcherInterface $eventDispatcher
 * @var array                    $module
 * @global                       $eventDispatcher @see ModulesApplication::loadCustomModule
 * @global                       $module          @see ModulesApplication::loadCustomModule
 */

function createTextMessageGlobals(GlobalsInitializedEvent $event)
{
    $instruct = xl('Enable the system to automatically send email reminders to patients that have an email on file');
    $event->getGlobalsService()->createSection("Automatic Appointment Reminders", "Report");
    $setting = new GlobalSetting(xl('Enable Automatic Emails Reminders'), 'bool', '', $instruct);
    $event->getGlobalsService()->appendToSection("Automatic Appointment Reminders", "auto_reminders_enables", $setting);

}

/*$eventDispatcher->addListener(GlobalsInitializedEvent::EVENT_HANDLE, 'createTextMessageGlobals');
*/
$eventDispatcher->addListener(MenuEvent::MENU_UPDATE, 'oe_module_apptreminder_add_menu_item');