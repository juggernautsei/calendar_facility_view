<?php

/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

namespace Juggernaut\Text\Module\App\Services;

use PDOException;
use Juggernaut\Text\Module\App\Model\PdoConnection;
class UpdateCalendarStatus
{
    public array $data;
    //update calendar status if confirmed is in the message
    public ?string $site;
    public ?string $status;
    /**
     * @var mixed|null
     */
    private mixed $pid;
    private $pdo;
    private $db;

    public function __construct()
    {
	$conn = PdoConnection::getInstance();
	$conn->site = $_SESSION['site_id'];
	$this->pdo = $conn->pdoConn();
	$this->db = $conn->retrieveDb();
    }

    public function updateCalendarStatus(): void
    {
	$this->pid = self::getPatientId();
	if (!empty($this->pid)) {
	    self::updateNextCalendarAppointment();
	}
    }

    // from the incoming message get the patient id
    private function getPatientId(): ?int
    {
	//remove the +1 from the phone number
	$phone = substr($this->data['fromNumber'], 2);
	$sql =  "
	SELECT pid
	FROM patient_data
	WHERE phone_cell = ?
    ";
	$result = sqlQuery($sql, [$phone]);
	if (!is_array($result)) {
	    return null;
	}
	return $result['pid'];
    }

    //this assumes that the patient is replying about the very next appointment only
    private function updateNextCalendarAppointment(): void
    {
	//get the next appointment
	$sql = "SELECT pc_eid FROM openemr_postcalendar_events WHERE pc_pid = ? AND pc_eventDate > CURDATE() ORDER BY pc_eventDate LIMIT 1";
	$appt_pc_eid = sqlQuery($sql, [$this->pid]);

	if ($this->status == 'confirmed') {
	    //update the status of the appointment
	    $sql = "UPDATE `openemr_postcalendar_events` SET `pc_apptstatus` = 'SMS' WHERE `pc_eid` = ? ";
	    sqlStatement($sql, [$appt_pc_eid['pc_eid']]);
	} elseif ($this->status == 'x') {
	    //update the status of the appointment
	    $sql = "UPDATE `openemr_postcalendar_events` SET `pc_apptstatus` = 'x' WHERE `pc_eid` = ? ";
	    sqlStatement($sql, [$appt_pc_eid['pc_eid']]);
	}
    }
}
