<?php

/**
 *
 *  package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 */

namespace Juggernaut\SimpleStorageSolution\Module;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenEMR\Core\ModulesClassLoader;

require_once dirname(__DIR__) . "/oe-simple-storage-solution/vendor/autoload.php";

/**
 * @global EventDispatcherInterface $eventDispatcher Injected by the OpenEMR module loader;
 */

$bootstrap = new Bootstrap($eventDispatcher, $GLOBALS['kernel']);
$bootstrap->subscribeToEvents();

