<?php
//show errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

use Slim\Factory\AppFactory;

$ignoreAuth = true;

$GLOBALS['sql_string_no_show_screen'] = false;

require_once __DIR__ . '/../vendor/autoload.php';
require_once dirname(__DIR__, 4) . "/globals.php";

// Define route constant
const MODULE_ROUTE = '/interface/modules/custom_modules/oe-module-wound-care/public';

$basePath = $GLOBALS['webroot'] . MODULE_ROUTE;
$app = AppFactory::create();
$app->setBasePath($basePath);

require_once __DIR__ . '/../config/router.php';

// Run the application
$app->run();
