<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  All rights reserved
 */

use OpenEMR\Core\Header;

$dir = '/interface/modules/custom_modules/text-messaging-app';

?>
<!doctype html>
<html>

<head>
    <title><?php echo xlt('Home page'); ?></title>
    <?php Header::setupHeader(['common']) ?>
</head>
<body>
<div class="container m-5">
    <h1><?php echo xlt("Home View") ?>!</h1>
    <?php include_once "nav.php"; ?>
</div>
</body>
</html>
