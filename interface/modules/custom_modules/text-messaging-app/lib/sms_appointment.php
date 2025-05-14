<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  All rights reserved
 */

$ignoreAuth = true;
    require_once dirname(__DIR__, 4) . "/globals.php";


use Juggernaut\Text\Module\App\Model\NotificationModel;
use Juggernaut\Text\Module\App\Controllers\SendMessage;
use OpenEMR\Services\FacilityService;

function start_sms_reminders(): void
{
    $process = new NotificationModel();

    $personsToBeContacted = $process->getAppointments();
    foreach ($personsToBeContacted as $person) {
	if ($person['phone_cell'] == '') {
	    continue;
	}
	if ($person['hipaa_allowsms'] != 'YES') {
	    continue;
	}

	$message = message($person);
	$cellNumber = $process->stripDashesFromNumber($person['phone_cell']);
	$response = SendMessage::outBoundMessage((int)$cellNumber, $message, $person['pid']);

	$patient_info = $person['title'] . " " . $person['fname'] . " " . $person['mname'] . " " . $person['lname'] . "|||" . $person['phone_cell'] . "|||" . $person['email'];

	$sdate = date("Y-m-d H:i:s");
	$sql_loginsert = "INSERT INTO `notification_log` ( `iLogId` , `pid` , `pc_eid` , `sms_gateway_type` , `message` , `type` , `patient_info` , `smsgateway_info` , `pc_eventDate` , `pc_endDate` , `pc_startTime` , `pc_endTime` , `dSentDateTime` ) VALUES (NULL,?,?,?,?,?,?,?,?,?,?,?,?)";

	$safe = array($person['pid'], $person['pc_eid'], 'TEXTBELT', $message, 'SMS' || '', $patient_info, $response, $person['pc_eventDate'], $person['pc_endDate'], $person['pc_startTime'], $person['pc_endTime'], $sdate);

	sqlStatement($sql_loginsert, $safe);
	$m++;
    }
    echo "<h3><span style='color: green'>Sent " . $m . " messages</span></h3>";
}

/**
 * @throws DateMalformedStringException
 */
function message($person): string
{
    $facilityDetails = findFacility($person['pc_facility']);
    $apptDate = new DateTime($person['pc_eventDate']);
    $formattedDate = $apptDate->format('H:i m-d-Y');

    return xlt("You have an upcoming appointment on "
	. $formattedDate
	. " " . $person['pc_startTime']
	. ". at " . $person['name'] . " located at " . $facilityDetails['street']
	. " Please reply 'C' to Confirm or 'X' to 'Cancel'"
	. " and call "
	. $facilityDetails['phone']);
}

function findFacility($facilityId): array
{
    $facilityService = new FacilityService();
    return $facilityService->getById($facilityId);
}
