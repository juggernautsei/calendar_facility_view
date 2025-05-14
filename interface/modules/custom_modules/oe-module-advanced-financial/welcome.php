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
        <title>Welcome to the module</title>
        <?php echo Header::setupHeader(); ?>
    </head>
    <body>
        <div class="container-fluid">
            <div>
                <h1>Welcome</h1>
            </div>
            <div>
                <p>Our document workflow solutions make working with documents easy, more secure, and better for our planet.</p>
            </div>
            <div>
                <h2 class="font-weight-bold pb-4">Enterprise <span class="text-primary">cloud fax</span> for regulated industries</h2>
                <p class="pr-xl-9 font-size-md-font-weight-bold">The easiest way for both large and small businesses to achieve real digital transformation. Save time and money by eliminating hardware and outsourcing fax to the cloud. Easily integrate secure and reliable cloud fax into existing apps and workflows.</p>
            </div>
            <div>
                <p>Use the Fax Module selection from the main menu to configure the settings</p>
            </div>

        </div>
    </body>
</html>
