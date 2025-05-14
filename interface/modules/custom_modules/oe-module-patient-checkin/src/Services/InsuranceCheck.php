<?php

/**
 *
 *   package OpenEMR
 *   link    https://www.open-emr.org
 *   author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *   Copyright (c) 2022.
 */

namespace Juggernaut\Module\PatientCheckin;

global $srcdir;
require_once("$srcdir/patient.inc.php");

use OpenEMR\Services\InsuranceService;
class InsuranceCheck {

    public const INSURANCE_TABLE = 'insurance_data';

    public const INSURANCE_COMPANY_TABLE = 'insurance_companies';

    public function checkInsuranceDetails($patientId)
    {
        $insurance = new InsuranceService();
        $insuranceData = $insurance->getOneByPid($patientId, 'PRIMARY');
        return $insuranceData;
    }

    public function checkBalanceDetails($id): int
    {
        try {
            $balance = get_patient_balance($id, false, false);
            return intval($balance);
        } catch (\Exception $e) {
            return 0;
        }
    }


    public function getInsuranceCompanies($inId){
        try {
            $query = "SELECT name FROM insurance_companies WHERE id = ?";
            $nameOfProvider = sqlQuery($query, [$inId]);
            return $nameOfProvider['name'];
        } catch (\Exception $e) {
            error_log("Error during check-in process: " . $e->getMessage());
            return ['message' => 'An error occurred during the fetch insurance company process. Please try again later.'];
        }
    }

    public function getInsuranceData($pid, $companyId){

        $insuranceTable = InsuranceCheck::INSURANCE_TABLE;

        $query = "SELECT * FROM $insuranceTable WHERE provider = ? and pid = ?";
        $result = sqlQuery($query, [$companyId, $pid]);

        if ($result) {
            return $result;
        } else {
            return ['message' => 'No insurance data found'];
        }
    }

    public function updateInsurance($data){

        try {
            $insuranceTable = InsuranceCheck::INSURANCE_TABLE;

            if (!preg_match('/^\d{2}[\/-]\d{2}[\/-]\d{4}$/', $data['DOB'])) {
                return ['message' => 'Invalid date of birth format. Please use mm/dd/yyyy or mm-dd-yyyy.'];
            }

            $dob = date('Y-m-d', strtotime($data['DOB']));

            $sql = "UPDATE $insuranceTable SET
            `plan_name` = ?,
            `policy_number` = ?,
            `subscriber_DOB` = ?,
            `group_number` = ?,
            `subscriber_lname` = ?,
            `subscriber_mname` = ?,
            `subscriber_fname` = ?
        WHERE `id` = ?";

            sqlQuery($sql, [$data['plan_name'], $data['policy_number'], $dob , $data['group_number'], $data['l_name'], $data['m_name'], $data['f_name'], $data['id']]);

            return true;

        } catch (\Exception $e) {
            return ['message' => 'An error occurred: ' . $e->getMessage()];
        }

    }

}

?>
