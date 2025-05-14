<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.
 * All rights reserved
 */

namespace Juggernaut\Module\App;

use OpenEMR\Core\Kernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Bootstrap
{
    const MODULE_DIRECTORY = 'oe-patient-client-app';
    const MODULE_DIRECTORY_PATH = 'interface/modules/custom_modules';
    /**
     * @var EventDispatcherInterface The object responsible for sending and subscribing to events through the OpenEMR system
     */
    private $eventDispatcher;
    private string $installPath;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->installPath = self::MODULE_DIRECTORY_PATH . DIRECTORY_SEPARATOR . self::MODULE_DIRECTORY;
        $this->eventDispatcher = $dispatcher;
    }

    public function subscribeToEvents()
    {
    }

}
