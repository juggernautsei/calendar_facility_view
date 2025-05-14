<?php
/*
 * @package openemr
 * @link https://www.open-emr.org
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright (c) 2023
 * @license All Rights Reserved
 */

namespace OpenEMR\Modules\Clover\Controller;

class Receipt
{
    private string $session_id;
    private array $responseData;
    private string $reference;
    private string $checkDate;
    private string $depositDate;
    private mixed $payTotal;
    private string $createdTime;
    private string $modifiedTime;
    private string $globalAmount;
    private string $paymentType;
    private string $description;
    private string $adjustmentCode;
    private string $postToDate;

    public int $patientId;
    private string $paymentMethod;


    public function __construct($responseData, $pid)
    {
        $transactionDate = date('Y-m-d');
        $createdTime = date('Y-m-d H:i:s');
        $this->session_id = $this->getSessionId();
        $this->responseData = $responseData;
        $amount = $this->wholeNumberToDecimal($this->responseData['amount']);
        $this->responseData['amount'] = $amount;
        $this->reference = $this->responseData['id'] . '-Clover';
        $this->checkDate = $transactionDate;
        $this->depositDate = $transactionDate;
        $this->payTotal = $this->responseData['amount'];
        $this->createdTime = $createdTime;
        $this->modifiedTime = $createdTime;
        $this->globalAmount = '0.00';
        $this->paymentType = 'patient';
        $this->patientId = $pid;
        $this->description = $this->getPatientName();
        $this->adjustmentCode = 'patient_payment';
        $this->postToDate = $transactionDate;
        $this->paymentMethod = 'credit_card';
        $this->saveReceipt();
    }

    final public function saveReceipt(): void
    {
        $query = $this->buildReceipt();
        $stmt = sqlStatement($query, [
            $this->session_id,
            0,
            $this->reference,
            $this->checkDate,
            $this->depositDate,
            $this->payTotal,
            $this->createdTime,
            $this->modifiedTime,
            $this->globalAmount,
            $this->paymentType,
            $this->description,
            $this->adjustmentCode,
            $this->postToDate,
            $this->patientId,
            $this->paymentMethod
        ]);
    }

    private function buildReceipt(): string
    {
        return "INSERT INTO `ar_session` (
                        `session_id`, `payer_id`, `user_id`, `closed`,
                        `reference`, `check_date`, `deposit_date`, `pay_total`, `created_time`, `modified_time`,
                        `global_amount`, `payment_type`, `description`, `adjustment_code`, `post_to_date`,
                        `patient_id`, `payment_method`)
                VALUES (?,0,?,0,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    }

    private function getSessionId()
    {
        $query = "SELECT MAX(`session_id`) + 1 AS sessionid FROM `ar_session` ORDER BY `session_id` DESC LIMIT 1";
        $id = sqlQuery($query);
        if (empty($id['sessionid'])) {
            return 1;
        }
        return $id['sessionid'];
    }

    private function getPatientName(): string
    {
        $query = "SELECT `fname`, `lname` FROM `patient_data` WHERE `pid` = ?";
        $result = sqlQuery($query, [$this->patientId]);
        return $result['fname'] . ' ' . $result['lname'];
    }

    private function wholeNumberToDecimal($amount): string
    {
        $numberofdigits = strlen($amount);
        $offset = $numberofdigits - 2;
        $lasttwodigits = substr($amount, $offset);
        $digitsbeforethelasttwo = substr($amount, 0, -2);
        return $digitsbeforethelasttwo . "." . $lasttwodigits;
    }
}
