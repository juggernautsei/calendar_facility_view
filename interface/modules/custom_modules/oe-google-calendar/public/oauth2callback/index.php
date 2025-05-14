<?php


global $oauth2_client_id, $oauth2_redirect, $oauth2_secret, $title;

// prevent UI redressing
Header("X-Frame-Options: DENY");
Header("Content-Security-Policy: frame-ancestors 'none'");

require_once dirname(__DIR__, 6) . "/src/Common/Session/SessionUtil.php";
OpenEMR\Common\Session\SessionUtil::portalSessionStart();


$ignoreAuth_onsite_portal = true;
$site = explode('-', $_GET['state']);
$_SESSION['site_id'] = $site[0];
require_once dirname(__DIR__, 5) . '/globals.php';
require_once dirname(__DIR__) . '/config.php';

use \Juggernaut\OpenEMR\Modules\GoogleCalendar\Services\TokenServices;



$redirect_uri = "https://" . $_SERVER['HTTP_HOST'] . "/interface/modules/custom_modules/oe-google-calendar/public/oauth2callback";
$client = new Google_Client();
$clientSecret = dirname(__DIR__, 4) . '/custom_modules/oe-google-calendar/public/client_secret.json';
$client = new Google_Client();
$client->setAuthConfig($clientSecret);
$client->addScope(Google\Service\Calendar::CALENDAR);
$client->setRedirectUri($redirect_uri);
$client->setAccessType('offline');
$client->setIncludeGrantedScopes(true);
$service = new Google\Service\Calendar($client);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code'], $_SESSION['code_verifier']);
    //store token and refresh token in the database
    $save_token = new TokenServices();
    $save_token->token = $token;
    $issaved = $save_token->saveToken();
    $title = xlt("Google Calendar");
    $die_stmt = xlt("Google Calendar Token Saved close this window and refresh the setting page by clicking the gear icon");

    echo "<http><head><title>$title</title></head><body><p>$die_stmt</p><script>window.close();</script></body></http>";
} else {
    $no_code = xlt("No code returned by Google. Close this window and try again.");
    $request_Failed = xlt("Request Failed");

    echo "<http><head><title>$title</title></head><body><h1>$request_Failed</h1><h3>$no_code</h3></body></http>";
}

die;
