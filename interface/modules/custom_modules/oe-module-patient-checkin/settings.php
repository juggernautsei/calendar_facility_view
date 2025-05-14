<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

$ignoreAuth = true;
require_once dirname(__FILE__, 4) . '/globals.php';
require_once __DIR__ . '/vendor/phpqrcode/qrlib.php';

$tempDir = dirname(__DIR__, 4) . $GLOBALS['webroot'] . '/sites/' . $_SESSION['site_id'] . '/images/';

// figure out the web address of the installed
$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$webroot = $protocol . $_SERVER['HTTP_HOST'] . $GLOBALS['webroot'];
$checkInUrl = $webroot . '/interface/modules/custom_modules/oe-module-patient-checkin/public/';

//generating QR code
QRcode::png($checkInUrl, $tempDir . 'patient_checkin_qr.png', 'L', 4, 2);

use OpenEMR\Core\Header;

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Setting'); ?></title>
    <?php Header::setupHeader(['common']); ?>
    <style>
        @media print {
            #printPageButton {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="container text-center">
<div class="row mt-5">
    <div class="col-12 mt-5 mb-5">
        <h1><?php echo xlt('Patient Check-in'); ?></h1>
        <p><?php echo xlt('Scan to check in'); ?></p>
        <?php echo '<img src="' . $webroot . '/sites/' . $_SESSION['site_id'] . '/images/patient_checkin_qr.png" />'; ?>
    </div>
</div>
    <div class="row mt-5">
        <div class="col-12">
            <!-- Button trigger modal -->
            <button class="btn btn-primary" id="printPageButton" onclick="window.print()" ><?php echo xlt("Print") ?></button>
        </div>
    </div>

</div>
</body>
</html>
