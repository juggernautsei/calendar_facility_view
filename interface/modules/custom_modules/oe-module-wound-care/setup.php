<?php

/*
 * @package OpenEMR
 * @link    https://www.open-emr.org
 * author Sherwin Gaddis <sherwingaddis@gmail.com>
 * All Rights Reserved
 * @copyright Copyright (c) 2024.
 */


require_once dirname(__DIR__, 3) . '/globals.php';

use OpenEMR\Core\Header;


?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('JSE Wound Care Assistant') ?></title>
    <?php Header::setupHeader(); ?>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1><?php echo xlt('JSE Wound Care Assistant') ?></h1>
            <p><?php echo xlt("The pupose of this chatbot is the assist in creating documentation for woundcare"); ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <pre><?php  ?></pre>
        </div>
    </div>
</div>
</body>
</html>
