<?php

/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut\Text\Module\App\Controllers;

use Juggernaut\Text\Module\App\Model\NotificationModel;
class TextAppointmentStatusChange
{
    private array $change;
    public function __construct($data)
    {
        $this->change = $data;
        $this->contactPatient(new SendMessage());
    }
    private function contactPatient(SendMessage $sendMessage): void
    {
        $process = new NotificationModel();
        $phoneData = self::getPatientCell();
        if ($phoneData === null) {
            return;
        }
        $phone = $process->stripDashesFromNumber($phoneData);
        $message = self::updatePatientScheduleMsg();
        $phone = (int) $phone;
        if ($this->change['form_apptstatus'] == "-" ) {
            $sendMessage::outBoundMessage($phone, $message);
        }
    }
    private function getPatientCell()
    {
        //when the provider calendar is being updated there is no pid
        if (empty($this->change['form_pid'])) {
            return null;
        }
        $sql = "SELECT `phone_cell` FROM `patient_data` WHERE `pid` = ?";
        $result = sqlQuery($sql, $this->change['form_pid']);
        if (!is_array($result)) {
            return null;
        }
        return $result['phone_cell'];
    }

    private function updatePatientScheduleMsg(): string
    {
        $provider = self::grabProviderName();
        return xlt("Your " . $this->change['form_title'] . " appointment has been scheduled for "
            . $this->change['event_start_date'] . " at " . $this->change['form_hour'] . ":" . $this->change['form_minute']. " with "
            . $provider . " Please add to your calendar. Text back Confirmed or Cancel");
    }

    private function grabProviderName(): string
    {
        $sql = "SELECT `fname`, `lname` FROM `users` WHERE `id` = ?";
        $result = sqlQuery($sql, $this->change['form_provider']);

        // Handle case where result is not an array or doesn't contain expected keys
        if (!is_array($result) || !isset($result['fname'], $result['lname'])) {
            return 'the provider';
        }

        return $result['fname'] . ' ' . $result['lname'];
    }
}
