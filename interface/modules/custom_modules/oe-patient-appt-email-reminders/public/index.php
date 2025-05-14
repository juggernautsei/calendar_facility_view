<?php

/**
 * package    Patient Reminder Module
 *  link      https://affordablecustomehr.com
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 */

require_once dirname(__FILE__, 5) . "/globals.php";
require_once dirname(__FILE__) . "/../vendor/autoload.php";

use OpenEMR\Core\Header;
use OpenEMR\Common\Csrf\CsrfUtils;
use Juggernaut\Modules\Database;


if (!empty($_POST['form_fromdate'])) {
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }

    $fetch = new Database();

    $response = $fetch->getNotification($_POST['form_fromdate'], $_POST['form_todate']);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt("Documentation Reminders"); ?></title>
    <?php Header::setupHeader(['common', 'datetime-picker'])?>
</head>
<body>
    <div class="container-lg main-container m-6">
        <div class="m-3">
            <h1><?php echo xlt('Notification Log'); ?></h1>
		</div>
		<div class="m-4">
            <form class="row row-cols-lg-auto g-3" method="post" name="email_notifications" action="index.php" onsubmit="return compare_dates();">
                <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                <input type="hidden" name="form_refresh" id="form_refresh" value="" />
                <input type="hidden" name="form_export" id="form_export" value="" />
                <div class="col">
                    <label for="form_fromdate"><?php echo xlt('Notifications Date Between'); ?>:</label>
                    <input type="text" size='10' class='form-control datepicker' name='form_fromdate' id='form_fromdate' value="<?php echo $_POST['form_fromdate'] ?>">
                </div>
                <div class="col">
                    <label for="form_todate"><?php echo xlt('To Date'); ?>:</label>
                    <input type="text" size='10' class='form-control datepicker' name='form_todate' id='form_todate' value="<?php echo $_POST['form_todate'] ?>">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary" onclick='$("#form_refresh").attr("value","true");'>Submit</button>
                    <?php
                    if (($_POST['form_refresh'] === 'true') || ($_POST['form_export'] === 'true')) {
                        ?>
                        <button type="submit" class="btn btn-primary" onclick='dropReport(); $("#form_export").attr("value","true");'>Export</button>
                        <?php
                    }
                    ?>
                </div>
            </form>
		</div>
        <div class="table m-2">
            <table class="table table-striped">
                <th scope="col"><?php echo xlt('PID'); ?></th>
                <th scope="col"><?php echo xlt('Message'); ?></th>
                <th scope="col"><?php echo xlt('Subject'); ?></th>
                <th scope="col"><?php echo xlt('Patient Name'); ?></th>
                <th scope="col"><?php echo xlt('Phone'); ?></th>
                <th scope="col"><?php echo xlt('Email Address'); ?></th>
                <th scope="col"><?php //echo xlt(''); ?></th>

                <?php
                    var_dump($response);
                ?>
            </table>
        </div>
		&copy; <?php echo date('Y') . " Juggernaut Systems Express" ?>
    </div>
</body>
<script>

    function compare_dates() {
        let fromDate = document.getElementById('form_fromdate').value;
        let toDate = document.getElementById('form_todate').value;
        if (!fromDate || !toDate) {
            alert('Date cannot be empty ')
            return false;
        }

        if (fromDate > toDate) {
            alert('To date cannot be older than From Date');
            return false;
        }
    }
    // jQuery stuff to make the page a little easier to use

    $(function () {
        $(".save").click(function() { top.restoreSession(); document.my_form.submit(); });
        $(".dontsave").click(function() { parent.closeTab(window.name, false); });

        $('.datepicker').datetimepicker({
            <?php $datetimepicker_timepicker = false; ?>
            <?php $datetimepicker_showseconds = false; ?>
            <?php $datetimepicker_formatInput = false; ?>
            <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
            <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
        });
    });
</script>
</html>