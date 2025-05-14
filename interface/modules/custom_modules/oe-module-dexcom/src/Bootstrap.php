<?php

/*
 *   package   OpenEMR
 *   link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c)
 *  All rights reserved
 *
 */

namespace Juggernaut\Dexcom\Module;

use OpenEMR\Events\Globals\GlobalsInitializedEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Bootstrap
{
    const MODULE_INSTALLATION_PATH = "/interface/modules/custom_modules/";
    const MODULE_NAME = "oe-module-dexcom";
    const DEXCOM_REDIRECT = "https://sites.affordablecustomehr.com";
    const DEXCOM_REDIRECT_FOLDER = "public";
    const DEXCOM_REDIRECT_FILE = "access.php";
    const DEXCOM_CLIENT_SECRET = "3hXRAsspfWuG9WvV";
    const DEXCOM_CLIENT_ID = "Byl1Uc1KIiomTdUz7y4rxXfrQqsfWSZh";

    const DEXCOM_AUTH_URI = "https://sandbox-api.dexcom.com";

    /**
     * @var EventDispatcherInterface The object responsible for sending and subscribing to events through the OpenEMR system
     */
    private $eventDispatcher;
    /**
     * @var DexcomGlobalConfig Holds our module global configuration values that can be used throughout the module.
     */
    private DexcomGlobalConfig $globalsConfig;
    private $eventDispacher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        //global $GLOBALS;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function subscribeToEvents(): void
    {
        $this->addGlobalSettings();
        $navBar = new NavBarSubscriber();
        $this->eventDispatcher->addSubscriber($navBar);
    }

    public function addGlobalSettings(): void
    {
        /*$this->eventDispacher->addListener(GlobalsInitializedEvent::class, function (GlobalsInitializedEvent $event) {

        });*/
    }

}
