<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * All rights reserved
 */

namespace Juggernaut\ProviderDashboard\Module;


/**
 * @global OpenEMR\Core\ModulesClassLoader $classLoader
 */
$classLoader->registerNamespaceIfNotExists('Juggernaut\\ProviderDashboard\\Module\\', __DIR__ . DIRECTORY_SEPARATOR . 'src');

/**
 * @global EventDispatcher $eventDispatcher Injected by the OpenEMR module loader;
 */
$bootstrap = new Bootstrap($eventDispatcher, $GLOBALS['kernel']);
$bootstrap->subscribeToEvents();

