<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * All rights reserved
 */

use Juggernaut\Module\HospitalCharge\Controllers\RapidBillingController;
use Juggernaut\Module\HospitalCharge\Controllers\SearchController;
use Juggernaut\Module\HospitalCharge\App\Exceptions\RouteNotFoundException;
use Juggernaut\Module\HospitalCharge\App\Home;


require_once dirname(__DIR__, 4) . "/globals.php";

const MODULE_NAME = 'oe-module-hospital-charge';

$router = new Juggernaut\Module\HospitalCharge\App\Router();
$webroot = $GLOBALS['webroot'];
$routePath = $webroot . "/interface/modules/custom_modules/oe-module-hospital-charge/public/index.php";

//you can create a router in your module to handle requests
//These are just ideas to make your module more dynamic
$router
    ->get($routePath . "/home", [Home::class, 'home'])
    ->get($routePath . "/about", [Home::class, 'about'])
    ->get($routePath . "/contact", [Home::class, 'contact'])
    ->get($routePath . "/searchPatient/{term}", [SearchController::class, 'findPatient'])
    ->get($routePath . "/searchProvider/{term}", [SearchController::class, 'findProvider'])
    ->get($routePath . "/searchCpt4/{type}/{term}", [SearchController::class, 'findCode'])
    ->post($routePath . "/startClaim", [RapidBillingController::class, 'rapidClaim'])
;



// Run the router
try {
    echo $router->resolve($_SERVER['REQUEST_URI'], strtolower($_SERVER['REQUEST_METHOD']));
} catch (RouteNotFoundException $e) {
    echo $e->getMessage();
}
