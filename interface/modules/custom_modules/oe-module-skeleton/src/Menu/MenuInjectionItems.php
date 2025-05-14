<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

namespace Skeleton\Module\Menu;

use OpenEMR\Menu\MenuEvent;
use stdClass;
class MenuInjectionItems
{
    public MenuEvent $event;

    public function SkeletonMenuItem(): MenuEvent
    {
        $menu = $this->event->getMenu();
        $menuItem = new stdClass();
        $menuItem->requirement = 0;
        $menuItem->target = 'ske';
        $menuItem->menu_id = 'ske0';
        $menuItem->label = xlt("Skeleton Module");
        $menuItem->url = "/interface/modules/custom_modules/oe-module-skeleton/public/index.php/dashboard";
        $menuItem->children = [];
        $menuItem->global_req = [];

        foreach ($menu as $item) {
            if ($item->menu_id == 'modimg') {
                $item->children[] = $menuItem;
                break;
            }
        }
        $this->event->setMenu($menu);
        return $this->event;
    }
}
