<?php

/*
 * @package openemr
 * @link https://www.open-emr.org
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright (c) 2023
 * @license All Rights Reserved
 */

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once dirname(__FILE__, 5) . '/globals.php';

use OpenEMR\Modules\Clover\Controller\CloverPayCharge;
use OpenEMR\Modules\Clover\Controller\CreateCardToken;
use OpenEMR\Core\Header;
use OpenEMR\Modules\Clover\Controller\Receipt;

/*
 * Let's process post data and sanitize it
 */
$apiAccessKey = null;
$amount      = null;
$pid         = null;
$ipaddress   = null;
$cardtype    = null;
$cardnumber  = null;
$carddate    = null;
$cardyear    = null;
$cardcvv     = null;
$cardlast4   = null;
$cardfirst6  = null;
$cardcountry = null;

if (isset($_POST)) {
    $apiAccessKey  = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_SPECIAL_CHARS);
    $ipaddress     = filter_input(INPUT_POST, 'ipaddress', FILTER_SANITIZE_SPECIAL_CHARS);
    $pid           = filter_input(INPUT_POST, 'pid', FILTER_VALIDATE_INT);
    $amount        = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_SPECIAL_CHARS);
    $cardtype      = filter_input(INPUT_POST, 'cardtype', FILTER_SANITIZE_SPECIAL_CHARS);
    $cardnumber    = filter_input(INPUT_POST, 'cardnumber', FILTER_VALIDATE_INT);
    $carddate      = filter_input(INPUT_POST, 'card-date-month', FILTER_SANITIZE_SPECIAL_CHARS);
    $cardyear      = filter_input(INPUT_POST, 'card-date-year', FILTER_SANITIZE_SPECIAL_CHARS);
    $cardcvv       = filter_input(INPUT_POST, 'card-cvv', FILTER_VALIDATE_INT);
    $cardlast4     = filter_input(INPUT_POST, 'card-last4', FILTER_VALIDATE_INT);
    $cardfirst6    = filter_input(INPUT_POST, 'card-first6', FILTER_VALIDATE_INT);
    $cardcountry   = filter_input(INPUT_POST, 'card-country', FILTER_SANITIZE_SPECIAL_CHARS);
}

$bodyJson = null;
$bodyJson = json_encode([
    'card' => [
        'brand' => $cardtype,
        'number' => $cardnumber,
        'exp_month' => $carddate,
        'exp_year' => $cardyear,
        'cvv' => $cardcvv,
        'last4' => $cardlast4,
        'first6' => $cardfirst6,
        'country' => $cardcountry
    ]
]);
//tokenize card
$key_token = new CreateCardToken($apiAccessKey, $bodyJson);
//returned tokenized card 10 minutes from now expires
$token_response = $key_token->tokenizeCard();
//decode tokenized card
$returnedTokenizedCard = json_decode($token_response, true);
//value to use for testing debugging
$c_amount = str_replace('.', "", $amount);
$c_amount = str_replace('$', "", $c_amount);

/**
 * Now create charge
 */
$charge = new CloverPayCharge();
$chargeJson = json_encode([
    'ecomind' => 'ecom',
    'metadata' => [
        'existingDebtIndicator' => false
    ],
    'amount' => $c_amount,
    'currency' => 'USD',
    'external_reference_id' => mt_rand(999,9999),
    'external_customer_reference' => mt_rand(100,1000),
    'receipt_email' => 'jsemsb@yahoo.com',
    'source' => $returnedTokenizedCard['id'],
    'description' => 'NeuroSail'
]);

//
$charge->ipaddress = $ipaddress;
$charge->jsonBody = $chargeJson;
$charge_response = $charge->processPatientCharge();
$receipt_response = json_decode($charge_response, true);

if ($receipt_response['status'] == 'succeeded' && $_GET['page'] == 'portal') {
    $receipt = new Receipt($receipt_response, $pid);
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Payment Receipt'); ?></title>
    <?php Header::setupHeader(); ?>

</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center"><?php echo xlt('Payment Receipt'); ?></h1>
            <hr>
            <table class="table table-bordered">
                <thead><?php echo xlt('Payment Details'); ?></thead>
                <thead></thead>
                <tbody>
                <tr>
                    <td><?php echo xlt('Amount Paid'); ?>:</td>
                    <td> <?php echo "$" . $amount; ?></td>
                </tr>
                <tr>
                <td><?php echo xlt('Tranaction ID'); ?>:</td>
                <td> <?php echo $receipt_response['id']; ?></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-12 text-center">
            <h4><?php echo xlt('See your email for receipt'); ?></h4>
        </div>
    </div>
</div>
</body>
</html>
