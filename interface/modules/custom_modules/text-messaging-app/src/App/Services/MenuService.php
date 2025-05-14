<?php

namespace Juggernaut\Text\Module\App\Services;

use OpenEMR\Menu\MenuEvent;
use stdClass;

class MenuService
{
    public MenuEvent $event;

    public function TextingLogMenuItem(): MenuEvent
    {
        $menu = $this->event->getMenu();
        $menuItem = new stdClass();
        $menuItem->requirement = 0;
        $menuItem->target = 'tex';
        $menuItem->menu_id = 'tex0';
        $menuItem->label = xlt("Texting Log");
        $menuItem->url = "/interface/modules/custom_modules/text-messaging-app/public/index.php/notifications";
        $menuItem->children = [];
        $menuItem->acl_req = ["patients", "docs"];
        $menuItem->global_req = [];

        foreach ($menu as $item) {
            if ($item->menu_id == 'patimg') {
                $item->children[] = $menuItem;
                break;
            }
        }

        $this->event->setMenu($menu);

        return $this->event;
    }
    public function AuditLogMenuItem(): MenuEvent
    {
        $menu = $this->event->getMenu();
        $menuItem = new stdClass();
        $menuItem->requirement = 0;
        $menuItem->target = 'tex';
        $menuItem->menu_id = 'tex0';
        $menuItem->label = xlt("Audit Log");
        $menuItem->url = "/interface/modules/custom_modules/text-messaging-app/public/index.php/auditlog";
        $menuItem->children = [];
        $menuItem->acl_req = ["patients", "docs"];
        $menuItem->global_req = [];

        foreach ($menu as $item) {
            if ($item->menu_id == 'patimg') {
                $item->children[] = $menuItem;
                break;
            }
        }

        $this->event->setMenu($menu);

        return $this->event;
    }

    public function TextSettingMenuItem(): MenuEvent
    {
        $menu = $this->event->getMenu();
        $menuItem = new stdClass();
        $menuItem->requirement = 0;
        $menuItem->target = 'tex';
        $menuItem->menu_id = 'tex3';
        $menuItem->label = xlt("Text Setting");
        $menuItem->url = "/interface/modules/custom_modules/text-messaging-app/public/settings.php";
        $menuItem->children = [];
        $menuItem->acl_req = ["patients", "docs"];
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

    public function BulkTextingMenuItem(): MenuEvent
    {
        $menu = $this->event->getMenu();
        $menuItem = new stdClass();
        $menuItem->requirement = 0;
        $menuItem->target = 'tex';
        $menuItem->menu_id = 'tex2';
        $menuItem->label = xlt("Send Bulk Text");
        $menuItem->url = "/interface/modules/custom_modules/text-messaging-app/public/bulk.php";
        $menuItem->children = [];
        $menuItem->acl_req = ["patients", "docs"];
        $menuItem->global_req = [];

        foreach ($menu as $item) {
            if ($item->menu_id == 'patimg') {
                $item->children[] = $menuItem;
                break;
            }
        }

        $this->event->setMenu($menu);

        return $this->event;
    }
}
