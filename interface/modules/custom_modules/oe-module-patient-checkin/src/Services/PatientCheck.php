<?php

/**
 *
 *   package OpenEMR
 *   link    https://www.open-emr.org
 *   author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *   Copyright (c) 2022.
 */

namespace Juggernaut\Module\PatientCheckin;

require_once("$srcdir/patient.inc.php");

use DateTime;
use OpenEMR\Services\AppointmentService;
class PatientCheck {

    public const PATIENT_TABLE = 'patient_data';

    public const INSURANCE_TABLE = 'insurance_data';
    public const APPOINTMENT_TABLE = 'openemr_postcalendar_events';
    private AppointmentService $appService;

    public function __construct()
    {
        $this->appService = new AppointmentService();
    }
    public function checkIn($name, $dob) {
        try {
            $patientTable = PatientCheck::PATIENT_TABLE;
            $appointmentTable = PatientCheck::APPOINTMENT_TABLE;
            $nameParts = explode(" ", $name);
            if (count($nameParts) < 2) {
                return ['message' => 'Please enter both first name and last name.'];
            }

            if (!preg_match('/^\d{2}[\/-]\d{2}[\/-]\d{4}$/', $dob)) {
                return ['message' => 'Invalid date of birth format. Please use mm-dd-yyyy or mm/dd/yyyy.'];
            }
            $patient = [];
            $appointment = [];

            if (count($nameParts) == 1) {
                $fName = $nameParts[0];
                $lName = "";
            } else {
                $fName = $nameParts[0];
                $lName = $nameParts[1];
            }

            $dob = DateTime::createFromFormat('m-d-Y', $dob);
            $dob = $dob->format('Y-m-d');

            $query = "SELECT * FROM $patientTable WHERE fname = ? AND lname = ? AND DOB = ?";
            $patient = sqlQuery($query, [$fName, $lName, $dob]); //for a single result using sqlQuery
            $today = date('Y-m-d');
            $GLOBALS['pid'] = $patient['pid'];
            if (!empty($patient) && $patient['pid'] > 0) {

                //first check that the appointment is in the database
                $appointment = $this->selectTodayAppointment($patient['pid'], $today);

                //if no appointment is found, search for reoccurring appointments
                if (empty($appointment['pc_eid'])) {
                    //search if this is a reoccurring appointment
                    $this->lookForReoccurringAppointments($patient['pid'], $today);
                    $appointment = $this->selectTodayAppointment($patient['pid'], $today);
                }

            } else {
                return ['message' => 'See front desk for assistance missing no appointment on the calendar today'];
            }
            $eid = $appointment['pc_eid'];
            if ($appointment) {
                if ($appointment['pc_apptstatus'] == '@') {
                    header("Location: ../view/already_checkin_page.php");
                    exit();
                } else {
                    //create encounter and update appointment status
                    $enc = $this->appService->createEncounterForAppointment($eid);
                    $this->appService->updateAppointmentStatus($eid, '@', $GLOBALS['pid'], $enc);
                    return $patient;
                }
            } else {
                return ['message' => 'There is no appointment scheduled. See front desk for assistance'];
            }
        } catch (\Exception $e) {
            error_log("Error during check-in process: " . $e->getMessage());
            return ['message' => 'An error occurred during the check-in process. Please try again later.'];
        }
    }


    /**
     * @param $id
     * @return array|false|null
     */
    public function existingPatientInfo($id)
    {
        try {
            $patientTable = PatientCheck::PATIENT_TABLE;
            $query = "SELECT * FROM $patientTable WHERE id = ?";

            return sqlQuery($query, [$id]);
        } catch (\Exception $e) {
            error_log("Error fetching patient information: " . $e->getMessage());
            return null;
        }
    }


    /**
     * @param $data
     * @return array|string[]
     */
    public function updateInfo($data)
    {
        try {
            $patientTable = PatientCheck::PATIENT_TABLE;
            $nameParts = explode(" ", $data['patient_name']);
            if (count($nameParts) < 2) {
                return ['message' => 'Please enter both first name and last name.'];
            }

            if (!preg_match('/^\d{1,2}[\/-]\d{1,2}[\/-]\d{4}$/', $data['DOB'])) {
                return ['message' => 'Invalid date of birth format. Please use mm/dd/yyyy or mm-dd-yyyy.'];
            }

            $dob = date('Y-m-d', strtotime($data['DOB']));
            if (count($nameParts) == 1) {
                $fName = $nameParts[0];
                $lName = "";
            } else {
                $fName = $nameParts[0];
                $lName = $nameParts[1];
            }

            $sql = "UPDATE $patientTable SET
            `fname` = ?,
            `lname` = ?,
            `DOB` = ?,
            `street` = ?,
            `city` = ?,
            `state` = ?,
            `postal_code` = ?,
            `phone_cell` = ?
        WHERE `id` = ?";

            sqlQuery($sql, [$fName, $lName, $dob, $data['street'], $data['city'], $data['state'], $data['postal_code'], $data['phone_cell'], $data['id']]);

            $patient = $this->existingPatientInfo($data['id']);

            if ($patient) {
                return $patient;
            } else {
                return ['message' => 'Something went wrong'];
            }
        } catch (\Exception $e) {
            return ['message' => 'An error occurred: ' . $e->getMessage()];
        }
    }

    /**
     * @param $pid
     * @param $today
     * @return void
     */
    private function lookForReoccurringAppointments($pid, $today): void
    {
        require_once dirname(__DIR__, 6) . "/library/appointments.inc.php";
        //fetch next x appointments
        $event = fetchNextXAppts($today, $pid);
        if (!empty($event)) {
            //get the previous appointment needed info
            $selectPreviousAppointment = $this->getPreviousAppointments($pid, $today);
            //merge the previous appointment needed info with the next appointment
            if (!empty($selectPreviousAppointment)) {
                $event[0]['pc_eid'] = (int) $selectPreviousAppointment['pc_eid'];
                $event[0]['pc_time'] = $today . date(" H:i:s");
                $event[0]['pc_aid'] = (int) $selectPreviousAppointment['pc_aid'];
                $event[0]['pc_catid'] = (int) $selectPreviousAppointment['pc_catid'];
                $event[0]['pc_location'] = $selectPreviousAppointment['pc_location'];
                $event[0]['pc_billing_location'] = $selectPreviousAppointment['pc_billing_location'];
                $event[0]['pc_hometext'] = 'Patient Check-in';

            }
                $this->appService->insert($pid, $event[0]);
        }
    }

    /**
     * @param $pid
     * @param $today
     * @return array|false|null
     */
    private function getPreviousAppointments($pid, $today)
    {
        return sqlQuery("SELECT * FROM " .
            PatientCheck::APPOINTMENT_TABLE .
            " WHERE pc_pid = ? AND pc_eventDate < ? ORDER BY pc_eventDate DESC LIMIT 1", [$pid, $today]);
    }

    /**
     * @param $pid
     * @param $today
     * @return array|false|null
     */
    private function selectTodayAppointment($pid, $today) {
        return sqlQuery("SELECT * FROM " .
            PatientCheck::APPOINTMENT_TABLE .
            " WHERE pc_pid = ? AND pc_eventDate = ?", [$pid, $today]);
    }

    public function getPatientEmail(mixed $id): array
    {
        $patientTable = PatientCheck::PATIENT_TABLE;
        $query = "SELECT email_direct FROM $patientTable WHERE id = ?";

        return sqlQuery($query, [$id]);
    }

    public function buildURL(): string
    {
        $domain = $_SERVER['HTTP_HOST'];
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $module = 'oe-module-patient-checkin';
        return $protocol . $domain .$GLOBALS['webroot']. '/portal/home.php?site=default&whereto=#paymentcard';
    }
}

