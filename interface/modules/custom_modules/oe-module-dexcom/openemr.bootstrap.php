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

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

require_once dirname(__DIR__) . "/oe-module-dexcom/vendor/autoload.php";

/**
 * @global EventDispatcherInterface $eventDispatcher Injected by the OpenEMR module loader;
 */

$bootstrap = new Bootstrap($eventDispatcher);
$bootstrap->subscribeToEvents();
