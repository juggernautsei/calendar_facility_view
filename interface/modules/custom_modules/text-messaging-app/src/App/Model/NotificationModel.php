<?php

/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  All Rights reserved
 */

namespace Juggernaut\Text\Module\App\Model;

class NotificationModel
{
    public function getPatientTextMessages(): array
    {
        $sql = "SELECT `tmm`.`date`, `tmm`.`fromnumber`, `tmm`.`provider_id`,`tmm`.`text`, CONCAT(pd.fname, ' ', pd.lname) AS name, tmm.status " .
        " FROM `text_message_module` `tmm` ";
        if (!empty($_SESSION['pid'])) {
            $sql .= " JOIN `patient_data` pd ON CONCAT('+1', REPLACE(`pd`.`phone_cell`, '-', '')) = `tmm`.`fromnumber`";
            $sql .= " WHERE `tmm`.`fromnumber` = '+1" . str_replace("-", "", $this->getPatientCell()['phone_cell'])
                 . "' ORDER BY `tmm`.`id` DESC LIMIT 25";
        } else {
            $sql .= " JOIN `patient_data` pd ON CONCAT('+1', REPLACE(`pd`.`phone_cell`, '-', '')) = `tmm`.`fromnumber`";
            $sql .= " ORDER BY `tmm`.`id` DESC LIMIT 25";
        }
        $source = sqlStatement($sql);
        $dataArray = [];
        while($row = sqlFetchArray($source)) {
            $dataArray[] = $row;
        }

        return $dataArray;
    }

    /**
     * @return array|false|null
     */
    public function getPatientCell()
    {
        $sql = "SELECT `phone_cell` FROM `patient_data` WHERE `pid` = ? ";
        $pid = $_SESSION['pid'] ?? '';
        if (empty($pid)) {
            return [];
        }
        $phone = sqlQuery($sql, [$pid]);
        //remove spaces and hyphens from number
        return str_replace([' ', '-'], '', $phone['phone_cell']);

    }

    public function createMeetingId(): string
    {
        $newmeetingid = sqlQuery("select DOB from patient_data where pid = ?", [$_SESSION['pid']]);
        return md5($newmeetingid['DOB'] . $_SESSION['pid']);
    }

    public function getAppointments(): array
    {
        require_once dirname(__DIR__, 6) . '/../library/appointments.inc.php';

        $nDays = self::numberOfDays();
        $date = date("Y-m-d",strtotime($nDays));

        return fetchEvents($date, $date);
    }

    private function numberOfDays(): string
    {
        $days = round($GLOBALS['SMS_NOTIFICATION_HOUR']/24);
        //the idea is to be flexible up to 5 days

        switch ($days) {
            case 1:
                $numDays = '+1 days';
                break;

            case 2:
                $numDays = '+2 days';
                break;

            case 3:
                $numDays = '+3 days';
                break;

            default:
                $numDays = '+4 days';
        }
        return $numDays;
    }

    public function getTimeZoneInfo(): bool|array|null
    {
        return sqlQuery("SELECT gl_value FROM globals WHERE gl_name = 'gbl_time_zone'");
    }

    public function stripDashesFromNumber($number)
    {
        return preg_replace('/[^0-9]/', '', $number);
    }

    public function getLogEntries()
    {
        $gather = sqlStatement("SELECT * FROM `notification_log` WHERE `sms_gateway_type` = 'TEXTBELT' ORDER BY `iLogId` DESC LIMIT 100");
        $logentries = [];
        while ($frow = sqlFetchArray($gather)) {
            $logentries[] = $frow;
        }
        return $logentries;
    }

}
