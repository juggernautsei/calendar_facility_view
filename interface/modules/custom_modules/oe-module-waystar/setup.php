<?php

/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

require_once dirname(__DIR__, 3) . '/globals.php';

use OpenEMR\Core\Header;

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('WayStar Settings') ?></title>
    <?php Header::setupHeader(); ?>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1><?php echo xlt('WayStar Settings') ?></h1>
            <form action="" method="post">
                <div class="form-group">
                    <label for="waystar_username"><?php echo xlt('WayStar Username') ?></label>
                    <input type="text" class="form-control" id="waystar_username" name="waystar_username">
                </div>
                <div class="form-group">
                    <label for="waystar_password"><?php echo xlt('WayStar Password') ?></label>
                    <input type="password" class="form-control" id="waystar_password" name="waystar_password">
                </div>
                <button type="submit" class="btn btn-primary"><?php echo xlt('Save') ?></button>
            </form>
        </div>
    </div>
</body>
</html>
