<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * All rights reserved
 */

use Juggernaut\Module\PatientAlerts\AlertsManager;
use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;

require_once dirname(__FILE__, 5) . '/globals.php';
require_once("$srcdir/formatting.inc.php");

$module_config = 1;
$storeAlert = new AlertsManager();

if ($_POST['start_date'] ?? null && $_POST['end_date'] ?? null && $_POST['alert'] ?? null) {
    if (!CsrfUtils::verifyCsrfToken($_POST['csrf_token_form'])) {
        echo "<script>alert('Invalid CSRF token. Please try again.')</script>";
        die;
    }
    $storeAlert->alert = $_POST;
    $response = $storeAlert->setAlert();
    if (!$response) {
        echo "<script>alert('Alert was not stored. Please try again.')</script>";
        error_log($response);
    }
}

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Programmable Alerts</title>
    <?php Header::setupHeader('datetime-picker', 'common'); ?>
    <script>

        <?php require($GLOBALS['srcdir'] . "/restoreSession.php"); ?>

        $(function () {
            //var win = top.printLogSetup ? top : opener.top;
            //win.printLogSetup(document.getElementById('printbutton'));

            $('.datepicker').datetimepicker({
                <?php $datetimepicker_timepicker = false; ?>
                <?php $datetimepicker_showseconds = false; ?>
                <?php $datetimepicker_formatInput = true; ?>
                <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
                <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
            });
        });

    </script>
</head>
<body>
<div class="container">
    <div class="col-md-12">
        <div class="row mt-5">
            <div class="col-md-12">
                <h2 class="text-center"><?php print xlt("Programmable Alerts"); ?></h2>
                <?php if(!$_SESSION['pid']) {
                    print "<div class='alert alert-danger'>Please select a patient to use this module.</div>";
                    die;
                } ?>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <form class="form" method="post">
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="start_date"><?php print xlt("Start Date") ?></label>
                            <input type="text" class="datepicker form-control mt-2" name="start_date" id="start_date" value=""
                                   placeholder="<?php print xlt('Start Date') ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="end_date"><?php print xlt("End Date") ?></label>
                            <input type="text" class="datepicker form-control mt-2" name="end_date" id="end_date" value=""
                                   placeholder="<?php print xlt('End Date') ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <label for="alert"><?php print xlt('Alert'); ?></label>
                            <textarea class="form-control mt-2" name="alert" id="alert"></textarea>
                        </div>
                    </div>
                    <div class="form-row text-center">
                        <div class="col-md-12">
                            <input class="btn btn-primary mt-3" type="submit" value="Submit">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-md-12">
                <h2 class="text-center"><?php print xlt("Current Active Alerts"); ?></h2>
                <table class='table table-striped'>
                    <?php
                    $currentAlerts = $storeAlert->getAlerts();
                        print "<tr><th>" . xlt('Start Date') .
                            "</th><th>" . xlt('End Date') .
                            "</th><th>" . xlt('Alert') .
                            "</th><th>" . xlt('User') .
                            "</th><th></th></tr>";
                    while ($row = sqlFetchArray($currentAlerts)) {
                        print "<tr><td>" . oeFormatDateTime($row['start_date']) .
                            "</td><td>" . oeFormatDateTime($row['end_date']) .
                            "</td><td>" . text($row['alert']) .
                            "</td><td>" . text($row['userid']) .
                            "</td><td><button class='btn btn-primary' onclick='markDone(" . attr($row['id']) . ")'>" . xlt("Done") . "</button></td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div> <!-- end of container -->
</body>
<script>
    function markDone(id) {
        $.ajax({
            url: '/interface/modules/custom_modules/programmable-patient-alerts/public/markDone.php',
            type: 'POST',
            data: {id: id, csrf_token_form: '<?php echo attr(CsrfUtils::collectCsrfToken()); ?>'},

            success: function (response) {
                location.reload();
            }
            error: function (response) {
                alert(response);
            }
        });
    }
</script>
</html>
