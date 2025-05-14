<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  All Rights Reserved
 */

require_once dirname(__FILE__) . "/vendor/autoload.php";
require_once dirname(__FILE__, 4) . "/globals.php";

use OpenEMR\Core\ModulesClassLoader;
use Juggernaut\Text\Module\Bootstrap;


/**
 * @global EventDispatcher $eventDispatcher Injected by the OpenEMR module loader;
 * first step to refactor this module to the new design pattern
 */
$bootstrap = new Bootstrap($eventDispatcher, $GLOBALS['kernel']);
$bootstrap->subscribeToEvents();

