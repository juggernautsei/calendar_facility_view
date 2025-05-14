<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

use OpenEMR\Core\Header;


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Audit Log'); ?></title>
    <?php Header::setupHeader(['common',  'datatables', 'datatables-dt', 'datatables-bs']) ?>
    <script>
        $(function () {
            $('#auditTrail').DataTable({
                order: [[0, 'desc']],
            });
        });
    </script>
</head>
<body>
<div class="container-main m-5">
    <div class="mt-3">
        <h1><?php echo xlt('Audit Log'); ?></h1>
    </div>
    <div class="mt-3">
        <table class="table stripe" id="auditTrail">
            <caption><?php echo xlt('Text appointment reminders notification sent to patients'); ?></caption>
            <thead>
                <th scope="col"><?php echo xlt("iLogId"); ?></th>
                <th scope="col"><?php echo xlt("Status"); ?></th>
                <th scope="col"><?php echo xlt("Patient"); ?></th>
                <th scope="col"><?php echo xlt("Date Time Sent"); ?></th>
                <th scope="col"><?php echo xlt("Appointment Date"); ?></th>
            </thead>
            <tbody>
            <?php
                foreach ($this->params as $param) {
                    print "<tr>";
                    print "<td>" . $param['iLogId'] . "</td>";
                    $delivered = json_decode($param['smsgateway_info'], true);
                    if ($delivered['success'] == 'true') {
                        print "<td>" . xlt('Delivered') . "</td>";
                    } else {
                        print "<td>" . xlt('Unsuccessful') . "</td>";
                    }
                    $patientInfo = explode("|||", $param['patient_info']);
                    print "<td class='w-750'>" . text($patientInfo[0]) . " " . text($patientInfo[1]) . " " . text($patientInfo[2]) . "</td>";
                    print "<td>" . $param['dSentDateTime'] . "</td>";
                    print "<td>" . $param['pc_eventDate'] . " " . $param['pc_startTime'] . "</td>" ;
                    print "</tr>";
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
<div class="p-5">
<i><?php echo text('Juggernaut Systems Express'); ?></i> &copy; 2020 <?php  echo " - " . date('Y'); ?>
</div>
</html>
