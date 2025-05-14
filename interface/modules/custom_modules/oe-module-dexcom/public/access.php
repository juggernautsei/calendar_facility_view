<?php

/*
 *   package   OpenEMR
 *   link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c)
 *  All rights reserved
 *
 */

use Juggernaut\Dexcom\Module\DexcomAccessToken;
use Juggernaut\Dexcom\Module\DexcomAuth;
use Juggernaut\Dexcom\Module\DexcomStoreRefreshToken;


$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once dirname(__FILE__, 5) . "/globals.php";
require_once "../vendor/autoload.php";
use OpenEMR\Core\Header;
Header("X-Frame-Options: DENY");
Header("Content-Security-Policy: frame-ancestors 'none'");

new DexcomAuth();

$accessToken = DexcomAccessToken::requestAccessToken($_GET['code']);
$grantedAccess = json_decode($accessToken, true);

$token = new DexcomStoreRefreshToken($grantedAccess['refresh_token']);


echo "<html><head>";
echo "<title>" . xlt('Dexcom Readings Access') . "</title>";
echo Header::setupHeader();
echo "</head><body><div class='container mt-5'>";
echo "<h1>" . xlt('Dexcom Readings Access') . "</h1>";
echo "<p>" . xlt('Access Granted') . "</p>";
echo "<p>" . xlt('You can now close this window') . "</p>";
echo "<button onclick='closeTab()'>Close Tab</button><script>
function closeTab() {
  window.close(); // Close the current browser tab
}
</script>";
echo "</div></body></html>";
die();




