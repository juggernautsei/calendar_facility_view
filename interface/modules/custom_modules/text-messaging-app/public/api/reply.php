<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
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

use Juggernaut\Text\Module\App\Controllers\Credentials;
use Juggernaut\Text\Module\App\Controllers\StoreTexts;
use Twilio\TwiML\MessagingResponse;


$credentials = new Credentials();
$match = $credentials->decryptedCredentials();
// Check if the AccountSid from the request matches the one stored in the database
if ($match['accountSid'] !== $_POST['AccountSid']) {
    die;
}
// Set the content-type to XML to send back TwiML from the PHP Helper Library
header("content-type: text/xml");

$_SESSION['site_id'] = $_GET['site'];
$_SESSION['pid'] = $_GET['pid'];
file_put_contents('/var/www/html/logging/extra.log', $_GET['site'] . " " . $_GET['pid'], FILE_APPEND);
$saveInbound = new StoreTexts();
file_put_contents('/var/www/html/logging/inboundMessage.log', print_r($_POST, true));
$saveInbound->saveText($_POST);

$response = new MessagingResponse();
$response->message(
    "message received"
);

echo $response;

