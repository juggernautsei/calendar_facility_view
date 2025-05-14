<?php
/*
 * @package OpenEMR
 *
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.
 * @license "All rights reserved"
 */

namespace Juggernaut\Module\KPI\Services;

class DashboardServices
{
    private $firstDay;
    private $lastDay;

    public function __construct()
    {
        $this->firstDay = $this->getFirsDayOfLastMonth();
        $this->lastDay = $this->getLastMonth();
    }

    public function getLastMonth(): string
    {
        $date = new \DateTime();
        $date->modify('last day of last month');
        return $date->format('Y-m-d');
    }

    public function getFirsDayOfLastMonth(): string
    {
        $date = new \DateTime();
        $date->modify('first day of last month');
        return $date->format('Y-m-d');
    }

    public function getTotalScheduledAppointments()
    {
        $appt = sqlQuery("SELECT count(*) AS visits FROM `openemr_postcalendar_events` WHERE `pc_eventDate` BETWEEN ? AND ?", [$this->firstDay, $this->lastDay]);
        return $appt['visits'];
    }

    /**
     * @return mixed
     * This looks for the appointments marked as new patient in office and telehealth
     * TODO: get the category number from the postcalendar_categories table
     */
    public function getNewPatientsSeen()
    {
        $newPatientsinoffice = sqlQuery("SELECT count(*) AS newPatientsOffice FROM `openemr_postcalendar_events` WHERE `pc_eventDate` BETWEEN ? AND ? AND `pc_catid` = 20 AND pc_apptstatus = '>'", [$this->firstDay, $this->lastDay]);
        $newPatientstelehealth = sqlQuery("SELECT count(*) AS newPatientsTele FROM `openemr_postcalendar_events` WHERE `pc_eventDate` BETWEEN ? AND ? AND `pc_catid` = 10 AND pc_apptstatus = '>'", [$this->firstDay, $this->lastDay]);
        return $newPatientsinoffice['newPatientsOffice'] + $newPatientstelehealth['newPatientsTele'];
    }

    public function getVisitsCompleted()
    {
        $visits = sqlQuery("SELECT count(*) AS completed FROM `openemr_postcalendar_events` WHERE `pc_eventDate` BETWEEN ? AND ? AND `pc_apptstatus` = '>'", [$this->firstDay, $this->lastDay]);
        return $visits['completed'];
    }

    public function getDocumentedVisits()
    {
        $documented = sqlQuery("SELECT DISTINCT COUNT(*) AS documented FROM `forms` WHERE date BETWEEN ? AND ? AND formdir != 'newpatient' AND (formdir = 'soap' OR formdir = 'assessment_intake')", [$this->firstDay, $this->lastDay]);
        return $documented['documented'];
    }

    public function getBilledVisits(): int
    {
        $countBilled = [];
        $billed = sqlStatement("SELECT encounter FROM `billing` WHERE `bill_date` BETWEEN ? AND ?  AND code_type = 'CPT4'", [$this->firstDay . '%', $this->lastDay . '%']);
        while ($row = sqlFetchArray($billed)) {
                $countBilled[] = $row['encounter'];
        }
        $num = array_unique($countBilled);
        return count($num);
    }

    public function getNoShow()
    {
        $noShowsBill = sqlQuery("SELECT count(*) AS noShowBill FROM `openemr_postcalendar_events` WHERE `pc_eventDate` BETWEEN ? AND ? AND `pc_apptstatus` = ?", [$this->firstDay, $this->lastDay, 'NSB']);
        $noShows = sqlQuery("SELECT count(*) AS noShows FROM `openemr_postcalendar_events` WHERE `pc_eventDate` BETWEEN ? AND ? AND `pc_apptstatus` = ?", [$this->firstDay, $this->lastDay, '?']);
        return $noShowsBill['noShowBill'] + $noShows['noShows'];
    }

    public function getCancelled()
    {
        $cancelled = sqlQuery("SELECT count(*) AS cancelled FROM `openemr_postcalendar_events` WHERE `pc_eventDate` BETWEEN ? AND ? AND `pc_apptstatus` = ?", [$this->firstDay, $this->lastDay, 'x']);
        return $cancelled['cancelled'];
    }

    public function getRescheduled()
    {
        $rescheduled = sqlQuery("SELECT count(*) AS rescheduled FROM `openemr_postcalendar_events` WHERE `pc_eventDate` BETWEEN ? AND ? AND `pc_apptstatus` = ?", [$this->firstDay, $this->lastDay, '+']);
        return $rescheduled['rescheduled'];
    }

    public function getCancelledByProvider()
    {
        $cancelledByProvider = sqlQuery("SELECT count(*) AS cancelledByProvider FROM `openemr_postcalendar_events` WHERE `pc_eventDate` BETWEEN ? AND ? AND `pc_apptstatus` = ?", [$this->firstDay, $this->lastDay, 'cbp']);
        return $cancelledByProvider['cancelledByProvider'];
    }

    public function getTotalCharges()
    {
        $charges = sqlQuery("SELECT SUM(`fee`) AS charges FROM `billing` WHERE `date` BETWEEN ? AND ?", [$this->firstDay . '%', $this->lastDay . '%']);
        return $charges['charges'];
    }

    public function getAdjustments()
    {
        $adjustments = sqlQuery("SELECT SUM(adj_amount) AS adjustments FROM `ar_activity` WHERE `post_time` BETWEEN ? AND ? ", [$this->firstDay . '%', $this->lastDay . '%']);
        return $adjustments['adjustments'];
    }

    public function collections(): array
    {
        $charges = [];
        $adjustments = [];
        $collections = sqlStatement("SELECT f.id, f.date, f.pid, CONCAT(w.lname, ', ', w.fname) AS provider_id, f.encounter,
f.last_level_billed, f.last_level_closed, f.last_stmt_date, f.stmt_count, f.invoice_refno,
p.fname, p.mname, p.lname, p.street, p.city, p.state, p.postal_code, p.phone_home, p.ss, p.billing_note,
p.pubpid, p.DOB, CONCAT(u.lname, ', ', u.fname) AS referrer,
( SELECT bill_date FROM billing AS b WHERE b.pid = f.pid AND b.encounter = f.encounter AND b.activity = 1
AND b.code_type != 'COPAY' LIMIT 1) AS bill_date, ( SELECT SUM(b.fee) FROM billing AS b WHERE b.pid = f.pid
AND b.encounter = f.encounter AND b.activity = 1 AND b.code_type != 'COPAY' ) AS charges,
( SELECT SUM(b.fee) FROM billing AS b WHERE b.pid = f.pid AND b.encounter = f.encounter AND b.activity = 1 AND b.code_type = 'COPAY' ) AS copays,
( SELECT SUM(s.fee) FROM drug_sales AS s WHERE s.pid = f.pid AND s.encounter = f.encounter ) AS sales,
( SELECT SUM(a.pay_amount) FROM ar_activity AS a WHERE a.pid = f.pid AND a.encounter = f.encounter AND a.deleted IS NULL) AS payments,
( SELECT SUM(a.adj_amount) FROM ar_activity AS a WHERE a.pid = f.pid AND a.encounter = f.encounter AND a.deleted IS NULL) AS adjustments FROM form_encounter AS f
JOIN patient_data AS p ON p.pid = f.pid LEFT OUTER JOIN users AS u ON u.id = p.ref_providerID
LEFT OUTER JOIN users AS w ON w.id = f.provider_id
WHERE f.date >= ? AND f.date <= ?  ORDER BY f.pid, f.encounter", [$this->firstDay . '%', $this->lastDay . '%']);
        while ($row = sqlFetchArray($collections)) {
            $charges[] = $row['charges'];
            $adjustments[] = $row['adjustments'];
        }
        $sumCharges = array_sum($charges);
        $sumAdjustments = array_sum($adjustments);
        return [$sumCharges, $sumAdjustments];
    }

    public function insuranceDistribution(): array
    {
        $query = "SELECT b.pid, b.encounter, SUM(b.fee) AS charges, " .
            "MAX(fe.date) AS date " .
            "FROM form_encounter AS fe, billing AS b " .
            "WHERE fe.date >= ? AND fe.date <= ? " .
            "AND b.pid = fe.pid AND b.encounter = fe.encounter " .
            "AND b.code_type != 'COPAY' AND b.activity > 0 AND b.fee != 0 " .
            "GROUP BY b.pid, b.encounter ORDER BY b.pid, b.encounter";

        $res = sqlStatement($query, array($this->firstDay . '%', $this->lastDay . '%'));
        $insarr = array();
        $prev_pid = 0;
        $patcount = 0;

        while ($row = sqlFetchArray($res)) {
            $patient_id = $row['pid'];
            $encounter_date = $row['date'];
            $irow = sqlQuery("SELECT insurance_companies.name " .
                "FROM insurance_data, insurance_companies WHERE " .
                "insurance_data.pid = ? AND " .
                "insurance_data.type = 'primary' AND " .
                "(insurance_data.date <= ? OR insurance_data.date IS NULL) AND " .
                "insurance_companies.id = insurance_data.provider " .
                "ORDER BY insurance_data.date DESC LIMIT 1", array($patient_id, $encounter_date));
            $plan = (!empty($irow['name'])) ? $irow['name'] : '-- No Insurance --';
            $insarr[$plan]['visits'] = $insarr[$plan]['visits'] ?? null;
            $insarr[$plan]['visits'] += 1;
            $insarr[$plan]['charges'] = $insarr[$plan]['charges'] ?? null;
            $insarr[$plan]['charges'] += sprintf('%0.2f', $row['charges']);
            if ($patient_id != $prev_pid) {
                ++$patcount;
                $insarr[$plan]['patients'] = $insarr[$plan]['patients'] ?? null;
                $insarr[$plan]['patients'] += 1;
                $prev_pid = $patient_id;
            }
        }
        return $insarr;
    }

    public function sortInsuranceDistribution($insarr)
    {
        $listInsurers = [];
        $charges = [];
        foreach ($insarr as $key => $val) {
            $listInsurers[] = "'" . $key . "'";
            $charges[] = $val['charges'];
        }

        return [$listInsurers, $charges];
    }
}
