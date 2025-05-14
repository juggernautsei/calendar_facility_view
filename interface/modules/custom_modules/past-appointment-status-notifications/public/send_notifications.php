<?php

/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once dirname(__DIR__, 3) . "/../globals.php";
require_once dirname(__DIR__, 4) . '/../library/patient.inc';
require_once __DIR__ . "/../vendor/autoload.php";

use Juggernaut\Notification;
use Juggernaut\NotificationModel;

/**
 * @return void
 */
function start_appt_notification()
{
    $checkApptStatus = new Notification();
    $providers = new NotificationModel();
    $contacts = $providers->getActiveProviders();
    $month = date('m');
    $yr = date('Y');
    $days_no = cal_days_in_month(CAL_GREGORIAN, $month, $yr);

    foreach ($contacts as $contact) {
        $twodaysago = new DateTime('28 days ago');

        try {
            $checkApptStatus->sendAlert($twodaysago->format('Y-m-d'), $contact);
        } catch (\PHPMailer\PHPMailer\Exception|phpmailerException $e) {
            file_put_contents('/var/www/html/errors/appt_notification_error.txt', $e->getMessage(), FILE_APPEND);
        }
    }

    $checkApptStatus->updateBackgroundServices($days_no);
}

