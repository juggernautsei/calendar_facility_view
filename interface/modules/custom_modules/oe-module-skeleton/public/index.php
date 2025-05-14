<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * All rights reserved
 */


//prevent UI redressing
use Skeleton\Module\App\Controllers\Dashboard;
use Skeleton\Module\App\Exceptions\RouteNotFoundException;

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once dirname(__DIR__, 4) . "/globals.php";


$router = new Skeleton\Module\Router();
$webroot = $GLOBALS['webroot'];
const MODULE_ROUTE = "/interface/modules/custom_modules/oe-module-skeleton/public/index.php";

$router
    ->get($webroot . MODULE_ROUTE . "/dashboard", [Dashboard::class, 'index']);

try {
    echo $router->resolve($_SERVER['REQUEST_URI'], strtolower($_SERVER['REQUEST_METHOD']));
} catch (RouteNotFoundException $e) {
    echo $e->getMessage();
}
