<?php

/**
 *
 *  package   OpenEMR
 *  link      https://affordablecustomehr.como
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 *
 */

require_once dirname(__FILE__, 5) . "/globals.php";
require_once dirname(__DIR__) . "/vendor/autoload.php";

use OpenEMR\Core\Header;

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt("Fill VA Document"); ?></title>
    <?php Header::setupHeader(['common'])?>
</head>
<body>
    <div class="container-lg">
        <div class = "">
            <?php
                $template = 'RFS-form-Blank.pdf';

                $fields = [
                    'form1[0].#subform[0].VAFacilityName[0]' => 'My Facility Name Here',
                ];

                $pdf = new FPDM($template);
                $pdf->Load($fields, false);
                $pdf->Merge();
                $pdf->Output();

            ?>
		</div>
		<div class="">
		</div>
		&copy; <?php echo date('Y') . " Juggernaut Systems Express" ?>
    </div>
</body>
</html>
