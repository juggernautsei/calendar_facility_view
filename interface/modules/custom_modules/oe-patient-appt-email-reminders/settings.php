<?php


/**
 * package    Patient Reminder Module
 *  link      https://affordablecustomehr.com
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 *
 */


require_once dirname(__FILE__, 4) . "/globals.php";
require_once  "vendor/autoload.php";

use OpenEMR\Core\Header;
use Juggernaut\Modules\Database;
use OpenEMR\Common\Csrf\CsrfUtils;

$enable = new Database();
if (!empty($_POST)) {
   if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
    $response = $enable->updateBackgroundService($_POST['enable']);
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Automated Appointment Email Reminders'); ?></title>
    <?php echo Header::setupHeader(); ?>
    
</head>
<body>
<div class="container m-5">
    <div class="form_container m-3">
        <h1><?php echo xlt('Automatic Email Reminders'); ?></h1>
        <p><?php echo xlt('The purpose of this module is to automate the process of sending appointment reminders to patients'); ?></p>
        <div>
        <?php if ($enable->getServiceStatus() == 0) {?>
            <form method="post" action="">
                <input type='hidden' name='enable' value='1'>
                <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                <input type=submit class="btn btn-primary" value="<?php echo xlt('Start Service'); ?>">
            </form>
        <?php } else { ?>
            <form method="post" action="">
                <input type='hidden' name='enable' value='0'>
                <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                <input type=submit class="btn btn-danger" value="<?php echo xlt('Stop Service'); ?>">
            </form>
        <?php } ?>
        </div>
    </div>
    <div class="m-3">
        <a class="btn btn-primary" href="public/index.php"><?php echo xlt('Notification Log'); ?></a>
    </div>
    &copy; <?php echo date('Y') . " Juggernaut Systems Express" ?>
</div>
</body>
</html>




