<?php

/*
 * @package openemr
 * @link https://www.open-emr.org
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright (c) 2023
 * @license All Rights Reserved
 */


/**
* @global OpenEMR\Core\ModulesClassLoader $classLoader
*/

use OpenEMR\Modules\Clover\Bootstrap;

$classLoader->registerNamespaceIfNotExists('OpenEMR\\Modules\\Clover\\', __DIR__ . DIRECTORY_SEPARATOR . 'src');

/**
 * @global EventDispatcher $eventDispatcher Injected by the OpenEMR module loader;
 */

$bootstrap = new Bootstrap($eventDispatcher, $GLOBALS['kernel']);
$bootstrap->subscribeToEvents();
