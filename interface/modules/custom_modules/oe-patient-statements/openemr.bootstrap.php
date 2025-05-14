<?php

/**
 *
 * @package     OpenEMR Weno Module
 *
 * @author      Jerry Padgett <sjpadgett@gmail.com>
 * @author      Kofi Appiah <kkappiah@medsov.com>
 * Copyright (c) 2023 Omega Systems Group Corp <omegasystemsgroup.com>
 * @license     GNU General Public License 3
 *
 */

namespace Juggernaut\Module\PatientStatements;


/**
 * @global OpenEMR\Core\ModulesClassLoader $classLoader
 */

$classLoader->registerNamespaceIfNotExists('Juggernaut\\Module\\PatientStatements\\', __DIR__ . DIRECTORY_SEPARATOR . 'src');

/**
 * @global EventDispatcher $eventDispatcher Injected by the OpenEMR module loader;
 */

$bootstrap = new Bootstrap($eventDispatcher, $GLOBALS['kernel']);
$bootstrap->subscribeToEvents();
