<?php

/**
 * package    Patient Reminder Module
 *  link      https://affordablecustomehr.com
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 */

namespace Juggernaut\Modules;

class Database
{

	
    function getNextRun()
    {
        $query = "SELECT next_run FROM background_services where active='1' and name=?";
        $result = sqlQuery($query, ['EMAIL_REMINDERS']);
        return $result['next_run'];        
    } 

    function updateBackgroundService($active){
        $query = "update background_services set  active = ? where name=?";
        try {
           sqlStatement($query, [$active, 'EMAIL_REMINDERS']);
        } catch (Exception $e) {
            return "An Error occurred " . $e->getMessage;
        }
        return 'success';
    }

    public function getServiceStatus() {
        $query = "SELECT active FROM background_services WHERE name = ?";
        $result = sqlQuery($query, ['EMAIL_REMINDERS']);
        return $result['active'];
    }
    public function getAppointmentEmailReminderDetails($params){
        $sql = "select patient_data.*, cal_events.pc_eventDate, cal_events.pc_startTime, cal_events.pc_endDate, cal_events.pc_endTime,  cal_date.last_appt,forms.last_visit from patient_data
                left outer join openemr_postcalendar_events as cal_events on patient_data.pid=cal_events.pc_pid
                and curdate() < cal_events.pc_eventDate left outer join (select pc_pid,max(pc_eventDate)
                as last_appt from openemr_postcalendar_events where curdate() >= pc_eventDate group by pc_pid )
                as cal_date on cal_date.pc_pid=patient_data.pid left outer join (select pid,max(date)
                as last_visit from forms where curdate() >= date group by pid)
                as forms on forms.pid=patient_data.pid where 1=1  and cal_events.pc_eventDate  BETWEEN ? and ?  and patient_data.email IS NOT NULL ";
        return sqlStatement($sql, $params);
    }
    public function getProviderDetail($pid){
        return sqlQuery("SELECT * FROM users WHERE id=?",[$pid]);
    }

    public function getNotification(string $fromDate, string $toDate): array
    {
        $response = [];
        $sql = "select * from notification_log where sms_gateway_type = ? and dSentDateTime BETWEEN ? AND ?";
        $results = sqlStatement($sql, ['sendgrid', $fromDate, $toDate]);
        while ($row = sqlFetchArray($results)) {
            $response[] = $row;
        }
        return $response;
    }

    public function getFacilityName()
    {
        return sqlQuery("select name from facility where id = 3");
    }
}