<?php

/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  All Rights Reserved
 */

header("Content-Type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, Access-Control-Allow-Headers, Authorizations, X-Requested-With');

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;

require_once dirname(__FILE__, 6) . "/globals.php";
require_once dirname(__FILE__, 3) . '/vendor/autoload.php';

use Juggernaut\Text\Module\App\Controllers\StoreTexts;
use OpenEMR\Common\Crypto\CryptoGen;
use Juggernaut\Text\Module\App\Controllers\ApiResponse;

$key = new CryptoGen();
if (!defined('CONST_INCLUDE_KEY')) {
	define('CONST_INCLUDE_KEY', $key->decryptStandard($GLOBALS['response_key']));
}


$uri = explode('/', $_SERVER['REQUEST_URI']);
$json = file_get_contents('php://input');

if ($uri[7] === 'reply') {
    $res = ApiResponse::getResponse('200');
    $messageData = json_decode($json, true);
    $saveDate = new StoreTexts();
    $_SESSION['site_id'] = $uri[8];
    if (isset($messageData['message'])) {
        $saveDate->saveText($messageData);
    } else {
        $res = ApiResponse::getResponse('400');
    }
    echo json_encode($res);
} else {
    $res = ApiResponse::getResponse('400');
    echo json_encode($res);
}


