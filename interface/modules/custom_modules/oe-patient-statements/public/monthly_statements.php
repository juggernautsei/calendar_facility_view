<?php

/*
 *   @package   OpenEMR
 *   @link      http://www.open-emr.org
 *
 *   @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *   Copyright (c)  Juggernaut Systems Express
 *   @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

use Juggernaut\Module\PatientStatements\Services\ProcessOutboundPlaceholders;
use OpenEMR\Common\Logging\SystemLogger;
use Juggernaut\Text\Module\App\Controllers\SendMessage;
use OpenEMR\Services\FacilityService;

require_once dirname(__DIR__, 4) . '/globals.php';
require_once dirname(__DIR__, 5) . '/library/patient.inc.php';


function sendPatientBalance(): void
{
    $firstDay = date('d');
    if ($firstDay != 12) {
        die;
    }
    $logger = new SystemLogger();
    $texting = isModuleEnabled();
    if (!$texting) {
        $msg = xlt('Text Messaging Module is not enabled');
        $logger->error($msg);
        die($msg);
    }
     $getPatientId = sqlStatement("SELECT pid FROM patient_data");
    $c = 1;

     while ($row = sqlFetchArray($getPatientId)) {
         $startSearchForBalance = get_patient_balance($row['pid']);
         if ($startSearchForBalance > 0) {
            $message = buildOutBoundMessage($startSearchForBalance);
            $phone = getPatientMobileNumber($row['pid']);
            if (empty($phone)) {
                $message = xlt("Patient does not have a mobile number");
                $level = xlt('error');
                $logger->log($level, $message);
                continue;
            }
            file_put_contents('/var/www/html/traps/outboundPatientlog.txt', $message . PHP_EOL, FILE_APPEND);
            sendPatientMessage($phone, $message);
             $c++;
         }
         $message = xlt("Number of patients with a balance: ") . $c;
         $level = xlt('info');
        $logger->log($level, $message);
     }
}

function isModuleEnabled(): bool
{
    $moduleSherwin = 'text-messaging-app';
    $moduleJerry = 'oe-module-faxsms';
    $sql = "SELECT * FROM `modules` WHERE `mod_directory` = ? OR `mod_directory` = ?";
    $result = sqlQuery($sql, [$moduleSherwin, $moduleJerry]);
    if ($result['mod_active'] == 1) {
        return $result['mod_directory'];
    }
    return false;
}

function sendPatientMessage($phone, $message): string
{
    $response = SendMessage::outBoundMessage($phone, $message);
    return $response;
}

function getPatientMobileNumber($pid)
{
    $sql = "SELECT `phone_cell` FROM patient_data WHERE `pid` = ?";
    $result = sqlQuery($sql, [$pid]);
    return $result['phone_cell'];
}

function getFacilityName(): array
{
    $facility = new FacilityService();
    return $facility->getPrimaryBillingLocation();
}

function buildOutBoundMessage($balance): string
{
    $msg = new ProcessOutboundPlaceholders();
    $office = getFacilityName();

    return $msg->replacePlaceholders($balance, $office['name'], date('m/d/Y'));
}




