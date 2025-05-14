<?php

/*
 *
 *   package   OpenEMR
 *   link      http://www.open-emr.org
 *   author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *   Copyright (c)
 *   All rights reserved
 */

namespace Juggernaut\Text\Module;

use Juggernaut\Text\Module\App\Services\Globals;
use Juggernaut\Text\Module\App\Services\MenuService;
use Juggernaut\Text\Module\App\Services\RemainingBalanceCount;
use OpenEMR\Core\Kernel;
use OpenEMR\Events\Globals\GlobalsInitializedEvent;
use OpenEMR\Menu\MenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenEMR\Events\Appointments\AppointmentSetEvent;
use Juggernaut\Text\Module\App\Controllers\TextAppointmentStatusChange;
use OpenEMR\Events\PatientDemographics\RenderEvent;
use Juggernaut\Text\Module\App\Services\DemographicButtonInjection;

class Bootstrap
{
    /**
     * @param MenuEvent $event
     * @return MenuEvent
     */

    /**
     * @var EventDispatcherInterface The object responsible for sending and subscribing to events through the OpenEMR system
     */
    private EventDispatcher $eventDispatcher;
    private string $textingKey;


    public function __construct(EventDispatcher $dispatcher, ?Kernel $kernel = null)
    {
	if (empty($kernel)) {
	    $kernel = new Kernel();
	}
	$this->eventDispatcher = $dispatcher;
	$this->textingKey = $GLOBALS['texting_enables'] ?? '';
    }

    public function subscribeToEvents(): void
    {
	$this->eventDispatcher->addListener(AppointmentSetEvent::EVENT_HANDLE, [$this, 'sendSmsUpdatePatientAppointment']);
	$this->eventDispatcher->addListener(RenderEvent::EVENT_RENDER_POST_PAGELOAD, [$this, 'renderButtonTextingPostLoad']);
	$this->eventDispatcher->addListener(MenuEvent::MENU_UPDATE, [$this, 'textingMenuItems']);
	$this->eventDispatcher->addListener(GlobalsInitializedEvent::EVENT_HANDLE, [$this, 'createTextMessageGlobals']);
	$this->eventDispatcher->addListener(\OpenEMR\Events\Main\Tabs\RenderEvent::EVENT_BODY_RENDER_POST, [$this, 'injectTextBalance']);

    }

    public function sendSmsUpdatePatientAppointment(AppointmentSetEvent $event): void
    {
	    new TextAppointmentStatusChange($event->givenAppointmentData());

    }

    public function renderButtonTextingPostLoad(RenderEvent $event): void
    {
	$inject = new DemographicButtonInjection();
	$inject->injectDemographicButton();
    }

    public function textingMenuItems(MenuEvent $event): void
    {
	$item = new MenuService();
	$item->event = $event;
	$item->AuditLogMenuItem();
	//$item->TextSettingMenuItem();
	$item->BulkTextingMenuItem();
	$item->TextingLogMenuItem();
    }

    public function createTextMessageGlobals(GlobalsInitializedEvent $event): void
    {
	$globals = new Globals();
	$globals->createTextMessageGlobals($event);
    }

    public function injectTextBalance(\OpenEMR\Events\Main\Tabs\RenderEvent $event): void
    {
	$inject = new RemainingBalanceCount();
	$inject->scriptRemainingBalanceCount();
    }
}
