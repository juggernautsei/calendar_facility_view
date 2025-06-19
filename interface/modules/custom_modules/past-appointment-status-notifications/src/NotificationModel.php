<?php
/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut;


class NotificationModel
{
    private $providerArray;
    private $providerId;

    public function __construct($days, $contact)
    {
        $this->pastDays = $days;
        $this->providerId = $contact;
    }

    public function hasPendingAppts()
    {
        $hasPendingAppts = $this->buildAppointmentList();
        if (!empty($hasPendingAppts)) {
            return $hasPendingAppts;
        } else {
            return xlt('No pending appointments found for ' . $this->pastDays);
        }
    }

    private function retrievePendingStatusAppts()
    {
        $sql = "SELECT `pc_eid`, `pc_pid`, `pc_aid`, `pc_title`, `pc_eventDate`, " .
            " `pc_apptstatus`, `pc_startTime` " .
            " FROM `openemr_postcalendar_events` WHERE `pc_apptstatus` = '^' AND " .
            " `pc_eventDate` BETWEEN ? AND ? AND `pc_pid` != '' AND `pc_aid` = ?";

        return sqlStatement($sql, [$this->pastDays, date('Y-m-d'), $this->providerId]);
    }

    protected function buildAppointmentList()
    {
        $list_ofAppointments = $this->retrievePendingStatusAppts();
        $pendingAppointments = [];

        while ($status = sqlFetchArray($list_ofAppointments))
        {
            $pendingAppointments[] = $status;
        }
        return $pendingAppointments;
    }

    public function getActiveProviders()
    {
        $sql = "SELECT `id` FROM users WHERE `active` = 1 AND `authorized` = 1";
        $this->providerArray = [];
        while ($irow = sqlFetchArray($sql)) {
            $this->providerArray[] = $irow;
        }
        return $this->providerArray;
    }

    public function getProviderEmailAddress($id)
    {
        return sqlQuery("SELECT `email` FROM `users` WHERE `id` = ?", [$id]);
    }
}