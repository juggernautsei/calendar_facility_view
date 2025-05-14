<?php

/**
 * package   OpenEMR
 *  link      https://affordablecustomehr.com
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 *
 */


require_once dirname(__FILE__, 4) . "/globals.php";

use OpenEMR\Core\Header;

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo xlt('Automated Appointment Email Reminders'); ?></title>
        <?php echo Header::setupHeader(); ?>
    </head>
    <body>
        <div class="container-fluid">
            <div>
                <h1><?php echo xlt('Welcome to version 1.0 of the automated email reminder'); ?></h1>
            </div>
            <div>
                <p></p>
            </div>
            <div>
                <h2 class="font-weight-bold pb-4">Enterprise <span class="text-primary">cloud fax</span> for regulated industries</h2>
                <p class="pr-xl-9 font-size-md-font-weight-bold"></p>
            </div>
            <div>
                <p></p>
                
            </div>

        </div>
    </body>
</html>
