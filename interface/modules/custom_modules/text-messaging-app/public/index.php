<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  All rights reserved
 */

use Juggernaut\Text\Module\App\Controllers\Credentials;
use Juggernaut\Text\Module\App\Controllers\Notification;
use Juggernaut\Text\Module\App\Controllers\TextIndividuals;
use Juggernaut\Text\Module\App\Controllers\Texting;
use Juggernaut\Text\Module\App\Exceptions\RouteNotFoundException;

require_once dirname(__DIR__, 4) . '/globals.php';
require_once __DIR__ . '/../vendor/autoload.php';

const VIEW_PATH = __DIR__ . '/../views';

$router = new Juggernaut\Text\Module\App\Router();
$webroot = $GLOBALS['webroot'];
const MODULE_ROUTE = "/interface/modules/custom_modules/text-messaging-app/public/index.php";

$router
    ->get($webroot . MODULE_ROUTE . "/notifications", [Notification::class, 'index'])
    ->get($webroot . MODULE_ROUTE . "/auditlog", [Notification::class, 'auditlog'])
    ->get($webroot . MODULE_ROUTE . "/individuals", [TextIndividuals::class, 'index'])
    ->get($webroot . MODULE_ROUTE . "/texting/sendTelehealthMessage", [Texting::class, 'sendTelehealthMessage'])
    ->post($webroot . MODULE_ROUTE . "/texting/bulk", [Texting::class, 'bulk'])
    ->post($webroot . MODULE_ROUTE . "/texting/individual", [Texting::class, 'individuals'])
    ->post($webroot . MODULE_ROUTE . "/texting/individualPatient", [Texting::class, 'individualPatient'])
    ->post($webroot . MODULE_ROUTE . "/credentials", [Credentials::class, 'saveCredentials'])
    ->post($webroot. MODULE_ROUTE . "/reply", [Texting::class, 'reply']);



try {
    echo $router->resolve($_SERVER['REQUEST_URI'], strtolower($_SERVER['REQUEST_METHOD']));
} catch (RouteNotFoundException $e) {
    echo $e->getMessage();
}
