<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  All Rights Reserved
 */

require_once dirname(__FILE__, 4) . "/globals.php";

$insurersId = 106;
$beginningDepositDate = date('Y') . '-01-01';
$endingDepositDate = date('Y') . '-01-31';

function insuranceIncome($beginningDepositDate, $endingDepositDate, $insurersId): float
{
    $paymentsforthemonth = "SELECT (ar_activity.pay_amount - ar_activity.adj_amount) AS net FROM `ar_activity`
    LEFT JOIN ar_session ON ar_session.session_id = ar_activity.session_id
    WHERE ar_session.payment_type = 'insurance'  AND ar_session.deposit_date BETWEEN ? AND ? AND ar_session.payer_id = ?
    AND ar_session.deposit_date IS NOT NULL AND ar_activity.deleted IS NULL";

    $totalpayments = sqlStatement($paymentsforthemonth, [$beginningDepositDate, $endingDepositDate, $insurersId]);

    $u = [];

    while ($iter = sqlFetchArray($totalpayments)) {
       $u[] = $iter['net'];
    }

    return array_sum($u);
}

function buildDataPoints($insurersId): array
{

    (int)$currentMonth = date('m');
    $jsonArray = [];
    $i = 1;
    while ($i <= $currentMonth) {
        $text = monthText($i);
        $depositDateInfo = depositDate($i);
        $monthIncome = insuranceIncome($depositDateInfo[0], $depositDateInfo[1], $insurersId);
        $jsonArray[] = ['label' => $text, 'y' => $monthIncome];
     $i++;
    }
   return $jsonArray;
}

function monthText($monthNumeral): string
{
    switch ($monthNumeral) {
        case 1:
            return "January";
        case 2:
            return "February";
       case 3:
           return "March";
       case 4:
            return "April";
       case 5:
           return "May";
       case 6:
           return "June";
       case 7:
            return "July";
       case 8:
           return "August";
       default:
            return 'Error processing month';
    }
}

function depositDate($month) {
        $depositArray = [];
    switch ($month) {
        case 1:
            $depositArray[] = date('Y') . '-01-01';
            $depositArray[] = date('Y') . '-01-31';
            return $depositArray;

        case 2:
            $depositArray[] = date('Y') . '-02-01';
            $depositArray[] = date('Y') . '-02-28';
            return $depositArray;

        case 3:
            $depositArray[] = date('Y') . '-03-01';
            $depositArray[] = date('Y') . '-03-31';
            return $depositArray;

        case 4:
            $depositArray[] = date('Y') . '-04-01';
            $depositArray[] = date('Y') . '-04-30';
            return $depositArray;

        case 5:
            $depositArray[] = date('Y') . '-05-01';
            $depositArray[] = date('Y') . '-05-31';
            return $depositArray;

        case 6:
            $depositArray[] = date('Y') . '-06-01';
            $depositArray[] = date('Y') . '-06-30';
            return $depositArray;

        case 7:
            $depositArray[] = date('Y') . '-07-01';
            $depositArray[] = date('Y') . '-07-31';
            return $depositArray;

        default:
            return xlt('Error finding deposit date array');
    }
}


echo "January ";
echo $january = insuranceIncome($beginningDepositDate, $endingDepositDate, $insurersId) . "<br>";

$beginningDepositDate = date('Y') . '-02-01';
$endingDepositDate = date('Y') . '-02-28';

echo "February ";
echo $february = insuranceIncome($beginningDepositDate, $endingDepositDate, $insurersId) . "<br>";

$beginningDepositDate = date('Y') . '-03-01';
$endingDepositDate = date('Y') . '-03-31';

echo "March ";
echo $march = insuranceIncome($beginningDepositDate, $endingDepositDate, $insurersId) . "<br>";

$beginningDepositDate = date('Y') . '-04-01';
$endingDepositDate = date('Y') . '-04-30';

echo "April ";
echo $april = insuranceIncome($beginningDepositDate, $endingDepositDate, $insurersId) . "<br>";


$beginningDepositDate = date('Y') . '-05-01';
$endingDepositDate = date('Y') . '-05-31';

echo "May ";
echo $may = insuranceIncome($beginningDepositDate, $endingDepositDate, $insurersId) . "<br>";

$beginningDepositDate = date('Y') . '-06-01';
$endingDepositDate = date('Y') . '-06-30';

echo "June ";
echo $may = insuranceIncome($beginningDepositDate, $endingDepositDate, $insurersId) . "<br><br><br>";


$dataPoints = buildDataPoints($insurersId);
echo "<pre>";
var_dump($dataPoints);
