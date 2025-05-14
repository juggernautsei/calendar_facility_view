<?php

Header("X-Frame-Options: DENY");
Header("Content-Security-Policy: frame-ancestors 'none'");

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code

require_once dirname(__DIR__, 4)."/globals.php";

use OpenEMR\Core\Header;

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Patient Checkin'); ?></title>
    <?php Header::setupHeader(['common']);?>
    <link rel="stylesheet" href="../style/main.css">

</head>
<body>
<div class="container">
    <div class="row mt-5">
        <div class="col-12 checkIn text-center">
            <img class="imageSize" src="../public/image/completed.svg" alt="" height="100px">
        </div>
    </div>
    <div class="row">
        <div class="col-5 mx-auto mt-2">
            <div class="form-group">
                <div class="text-center">
                    <h3><?php echo xlt('Check In Completed'); ?></h3>
                    <p><?php echo xlt('Please close browser window'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
