<?php

/*
 *   package   OpenEMR
 *   link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c)
 *  All rights reserved
 *
 */

global $pid, $dexcom_pid;


$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once dirname(__FILE__, 5) . "/globals.php";
require_once "../vendor/autoload.php";

use Juggernaut\Dexcom\Module\DexcomAuth;
use OpenEMR\Common\Csrf\CsrfUtils;

$name = 'dexcom';
$pid = $_SESSION['pid'];
$expiration = time() + (5 * 60); // 5 minutes * 60 seconds/minute
setcookie($name, $pid, $expiration, '', '', true, true);

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Dexcom Registration') ?></title>
</head>
<body>
<?php
new DexcomAuth();
$token = CsrfUtils::collectCsrfToken();
$dexcom = DexcomAuth::authBuildReturnUrl();
 //echo $dexcom;
    header('Location: ' . $dexcom . $token);
?>
</body>
</html>
