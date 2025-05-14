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

use Juggernaut\Module\PatientCheckin\Bootstrap;
use Symfony\Component\EventDispatcher\EventDispatcher;

$classLoader->registerNamespaceIfNotExists('Juggernaut\\Module\\PatientCheckin\\', __DIR__ . DIRECTORY_SEPARATOR . 'src');

/**
 * @global EventDispatcher $eventDispatcher Injected by the OpenEMR module loader;
 */

$bootstrap = new Bootstrap($eventDispatcher, $GLOBALS['kernel']);
$bootstrap->subscribeToEvents();
