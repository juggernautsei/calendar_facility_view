<?php
/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 * Copyright (c) 2023.
 */

namespace Juggernaut\Module\PatientAlerts;

class AlertsManager
{
    public mixed $alert;
    public int $alertId;
    public function setAlert(): bool|string
    {
        $start_date = DateToYYYYMMDD($this->alert['start_date']);
        $end_date = DateToYYYYMMDD($this->alert['end_date']);
        $alert = $this->alert['alert'];

        $sql = "INSERT INTO `module_simple_alerts` (userid, pid, start_date, end_date, alert, active) VALUES (?, ?, ?, ?, ?, ?)";
        $bindings = [];
        $bindings[] = $_SESSION['authUser'];
        $bindings[] = $_SESSION['pid'];
        $bindings[] = $start_date;
        $bindings[] = $end_date;
        $bindings[] = $alert;
        $bindings[] = 1;
        try {
            sqlStatement($sql, $bindings);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }
    public function getAlerts()
    {
        $sql = "SELECT * FROM `module_simple_alerts` WHERE pid = ? AND active = ?";
        $bindings = [];
        $bindings[] = $_SESSION['pid'];
        $bindings[] = 1;
        return sqlStatement($sql, $bindings);
    }

    public function getSinglePatientAlert($pid)
    {
        $sql = "SELECT alert FROM `module_simple_alerts` WHERE pid = ? AND active = ? AND start_date <= NOW() AND end_date >= NOW()";
        $bindings = [];
        $bindings[] = $pid;
        $bindings[] = 1;
        $alerts = sqlQuery($sql, $bindings);
        return $alerts['alert'];
    }

    public function markDone()
    {
        $sql = "UPDATE `module_simple_alerts` SET active = ? WHERE id = ?";
        $bindings = [];
        $bindings[] = 0;
        $bindings[] = $this->alertId;
        try {
            sqlStatement($sql, $bindings);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }
}
