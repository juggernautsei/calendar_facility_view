<?php

/*
 *   package   OpenEMR
 *   link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022-
 *  All rights reserved
 *
 */

use OpenEMR\Core\Header;

require_once dirname(__DIR__, 4) . '/globals.php';

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Purchase Credits'); ?></title>
    <?php Header::setupHeader(['common']); ?>
    <style>
        .navbar-light .navbar-nav .nav-link{
            color:white !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid main_container mt-5">
        <div class="row">
            <div class="mx-auto" style="width: 80%">
                <h1 class="text-center"><?php echo xlt('Buy Texting Credits'); ?></h1>
                <?php require_once dirname(__FILE__) . "/../views/nav_top.php"; ?>
                <div class="m-3">
                    <p><?php echo xlt('Below are the options for purchasing credits to use this module. This is a one time purchase '); ?></p>
                    <p><?php echo xlt('Your card will not be automatically billed. Once you run out of credits you can purchase more when you are ready. '); ?></p>
                </div>
                <div class="m-3">

                    <a   class="btn btn-secondary m-2 w-25"  href="https://link.waveapps.com/jtju7p-3c36rt"> $6  / 50 <?php echo xlt('Texts'); ?></a><br>
                    <a   class="btn btn-secondary m-2 w-25"  href="https://link.waveapps.com/36cwxd-asctrd"> $10 / 200 <?php echo xlt('Texts'); ?></a><br>
                    <a   class="btn btn-secondary m-2 w-25"  href="https://link.waveapps.com/57269k-jxmpsn"> $15 / 700 <?php echo xlt('Texts'); ?></a><br>
                    <a   class="btn btn-secondary m-2 w-25"  href="https://link.waveapps.com/br7cuw-7kqadq"> $33 / 1700 <?php echo xlt('Texts'); ?></a><br>
                    <a   class="btn btn-secondary m-2 w-25"  href="https://link.waveapps.com/5dnfza-v87jw8"> $55 / 3500 <?php echo xlt('Texts'); ?></a><br>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="mx-auto" style="width: 80%">
                <i><?php echo xlt('Juggernaut Systems Express'); ?></i> &copy; 2020 <?php  echo " - " . date('Y'); ?>
            </div>
        </div>
    </div>
    <script src="lib/js/nav.js"></script>
</body>
</html>


