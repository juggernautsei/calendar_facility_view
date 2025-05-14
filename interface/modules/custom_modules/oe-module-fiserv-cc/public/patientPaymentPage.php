<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
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

use OpenEMR\Modules\Clover\Controller\RetrieveAPIKey;
use OpenEMR\Core\Header;

$key_token = new RetrieveAPIKey();

$hasAccessToken = $key_token->checkAccessToken();

if (!$hasAccessToken) {
    echo "<title>" . xlt('Clover setup') . "</title>";
    echo "<h3>" . xlt('Clover does not have an access token') . "</h3>";
    echo "<p>" . xlt('Go to the module setup page to get Access Token') . "</p>";
    die;
}

$amount = $_GET['amount'];
$pid = (isset($_GET['pid'])) ? filter_input(INPUT_GET, 'pid', FILTER_SANITIZE_SPECIAL_CHARS) : $_SESSION['pid'];
$apiAccessKey = json_decode($key_token->cloverApiKey(), true);

if (isset($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS);
    $action = "thePaymentProcessor.php?page=" . $page;
} else {
    $action = "thePaymentProcessor.php";
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php print xlt("Clover Payment"); ?></title>
    <?php Header::setupHeader(); ?>
    <style>
        #spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            margin-left: -50px;
            margin-top: -50px;
            z-index: 2;
        }
    </style>
</head>
<body>
<div class="spinner-border  text-success" style="width: 5rem; height: 5rem;" id="spinner" role="status">
    <span class="sr-only">Loading...</span>
</div>
<div class="container">
    <h3 class="text-center mt-2"><?php print xlt("Clover Payment"); ?></h3>
    <div class="row mt-5">
        <form class="form" action="<?php echo $action; ?>" method="post" id="payment-form" onsubmit="return validateForm()">
            <?php
            // Check to see if there was an error and gracefully handle it
            echo $apiAccessKey['message'] ?? '';
                if (!empty($apiAccessKey['message'])) {
                    error_log($apiAccessKey['message'] . " - Clover Access Issue");
                    echo "<p>" . xlt("Clover Access Issue - Contact Support") . "</p>";
                    die;
                }
            ?>
            <input type="hidden" name="token" value="<?php echo isset($apiAccessKey['apiAccessKey']); ?>">
            <input type="hidden" name="ipaddress" value="<?php echo $_SERVER['REMOTE_ADDR'] ?? ''; ?>">
            <input type="hidden" name="pid" value="<?php echo $pid; ?>">
            <div class="form-row top-row">
                <div id="pay_amount_field" class="field card-number">
                    <input type='text' class="form-control m-1" name="amount" id="amount" placeholder="0.00" value="<?php echo $amount; ?>"
                    title="please use a decimal value or you will paying change and not dollars">
                </div>
            </div>
            <div class="form-row">
                <div class="col-6" id="card-number-errors" role="alert">
                    <input class="form-control m-1" name="cardnumber" id="cardnumber" placeholder="Card Number Digits Only">
                </div>
            </div>

            <div class="form-row">
                <div class="col-2" id="card-date-errors" role="alert">
                    <input class="form-control m-1" name="card-date-month" id="card-date-month" placeholder="MM">
                </div>
                <div class="col-2" id="card-date-errors" role="alert">
                    <input class="form-control m-1" name="card-date-year" id="card-date-year" placeholder="YY">
                </div>
                <div class="col-2" id="card-cvv-errors" role="alert">
                    <input class="form-control m-1" name="card-cvv" id ="card-cvv" placeholder="CVV">
                </div>
            </div>

            <div class="form-row">
                <div class="col-3" id="card-cvv-errors" role="alert">
                    <input class="form-control m-1" name="card-last4" id ="card-last4" placeholder="Card Last 4">
                </div>
                <div class="col-3" id="card-cvv-errors" role="alert">
                    <input class="form-control m-1" name="card-first6" id ="card-first6" placeholder="Card First 6">
                </div>
                <div class="col-3" id="card-postal-code-errors" role="alert">
                    <input class="form-control m-1" name="card-country" id="card-country" value="US" title="Must but two letters">
                </div>
            </div>

            <div id="card-response" role="alert"></div>
            <div class="button-container mt-3">
                <button id="submit-payment" class="btn btn-primary" ><?php print xlt("Submit Payment"); ?></button>
            </div>
        </form>
    </div>
</div>
</body>
<script>
    const form = document.getElementById("payment-form");
    const fields = form.querySelectorAll("input");

    //create a page loading spinner for the form submit
    const spinner = document.getElementById("spinner");
    form.addEventListener('submit', (e) => {
        spinner.style.display = "block";
    });

    function validateForm() {
        // Loop through the fields and check if they are all filled
        for (const field of fields) {
            if (field.value === "") {
                // One of the fields is not filled
                alert("Please fill in all fields before submitting. " + field.name + " missing.");
                return false;
            }
        }
        this.formYear();
        this.formMonth();
    }

    function formYear() {
        let year = document.getElementById('card-date-year');
        if (year.value.length > 2) {
            alert('Year must be 2 digits');
            return false;
        }
    }

    function formMonth() {
        let month = document.getElementById('card-date-month');
        if (month.value.length > 2) {
            alert('Month must be 2 digits');
            return false;
        }
    }

    $(document).ready(function () {
        // This is the function that will format the amount to currency
        $("#amount").on('change click keyup input paste', (function () {
            $(this).val(function (index, value) {
                return value
                    .replace(/(?!\.)\D/g, "")
                    .replace(/(?<=\..*)\./g, "")
                    .replace(/(?<=\.\d\d).*/g, "")
            })
        }))
    });
</script>
</html>

