<?php

/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

require_once dirname(__FILE__, 4) . '/globals.php';

use Juggernaut\Module\App\Services\CheckPayStatus;
use OpenEMR\Common\Uuid\UniqueInstallationUuid;
use OpenEMR\Core\Header;
use Juggernaut\Module\App\Services\CheckOutsideIpAddress;
use OpenEMR\Services\FacilityService;
use Juggernaut\Module\App\Services\StoreAppApiKey;

define('APP_REGISTRATION_URL', $_SERVER['SERVER_NAME']);

$ip = new CheckOutsideIpAddress();
$publicIP = $ip->getOutsideIpAddress();
$publicIP = trim($publicIP);
$https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
$checkDomainHttps = $ip->checkHttpsConnection($_SERVER['SERVER_NAME']);
$haskey = new StoreAppApiKey();
$keyValue = $haskey->getApiKey();
$hasPaid = new CheckPayStatus();

if (!empty($keyValue['api_key'])) {
    $status = $hasPaid->checkPaymentStatus($keyValue['remote_id'], $keyValue['api_key']);
} else {
    $status = [];
    $status['paid_status'] = 0;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo attr('Settings') ?></title>
    <?php Header::setupHeader() ?>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <h1 class="m-5"><?php echo xlt('OpenEMR Patient Client App'); ?></h1>
        <div class="tab">
            <button class="tablinks" onclick="openTab(Event, 'Home')"><?php echo xlt('Home'); ?></button>
            <button class="tablinks" onclick="openTab(Event, 'Register')"><?php echo xlt('Register'); ?></button>
            <button class="tablinks" onclick="openTab(Event, 'Settings')"><?php echo xlt('Settings'); ?></button>
            <button class="tablinks" onclick="openTab(Event, 'Contact')"><?php echo xlt('Support'); ?></button>
        </div>

        <div id="Home" class="tabcontent">
            <h3><?php echo xlt('Focus'); ?></h3>
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <?php echo xlt("The purpose of this module is to connect your patients with their data through their mobile device.
                        The patient client app is a mobile application that allows patients to access their medical records, schedule appointments, and communicate with their healthcare provider.
                        More features will be developed over time. Thank you for your support."); ?>
                    </p>

                    <h3><?php echo xlt('Cost') ?>:</h3>
                    <p>
                        <?php echo xlt('The cost of the patient client app is $48 per month.
                        This includes unlimited patients and unlimited data storage') ?>.
                    </p>
                    <p>
                        <?php echo xlt("The funds collected are to support the OpenEMR project. We have been building features for over 12 years.
                        The funds go for R&D, app store costs, and maintenance"); ?>
                    </p>
                    <p>
                        <?php echo xlt("Your patient, benefit the most from this work."); ?>
                    </p>
                    <p>
                        <?php echo xlt("Price subject to change."); ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <h3><?php echo xlt('Who can use this module') ?></h3>
                    <p>
                        <?php echo xlt('This module is designed for healthcare providers who want to provide their patients with a mobile application to access their medical records.
                        The patient client app is compatible with iOS and Android devices'); ?>.
                    </p>
                    <h3><?php echo xlt('How to get started') ?></h3>
                    <p>
                        <?php echo xlt('Your instance of OpenEMR must have a public IP address and be accessible from the internet.
                        Do not register this module if you are running this program on a laptop computer or a local server.
                        We have gotten your outside IP address and it is'); ?><br>
                        <?php echo xlt("Outside Address: ") . $publicIP; ?> <br>
                         <?php
                        if ($https) {
                            echo "HTTPS: <span class='text-success'>Yes (Secure) Good Job!</span>";
                        } else {
                            echo "HTTPS: <span class='text-danger'>No (Not Secure) Fix it!</span>";
                        }
                        ?><br>
                         <?php
                            echo "Domain Name: <span class='text-success'>" . APP_REGISTRATION_URL . "</span><br>";
                            if (APP_REGISTRATION_URL == "localhost") {
                                echo "<span class='text-danger'><strong>" . xlt('Fix it! You need a fully qualified domain name') . ".</strong></span>";
                                echo "<br><a href='https://en.wikipedia.org/wiki/Fully_qualified_domain_name'>" . xlt('What is a fully qualified domain name?') . "</a>";
                            }
                         ?>

                    </p>
                    <p>
                        <?php echo xlt('Click this link to make sure it works before registering for service') ?>.
                        <br>
                        <?php echo xlt("If you can't get there the app can't either, fix it"); ?>!<br>
                        <a href="https://<?php echo APP_REGISTRATION_URL . "/interface/smart/register-app.php" ?>">https://<?php echo APP_REGISTRATION_URL . "/interface/smart/register-app.php" ?></a>
                    </p>
                    <p><?php echo xlt("After clicking the link above if you see Not Authorized click the settings tab for instruction. "); ?></p>
                </div>
            </div>
        </div>
    <?php if ($https && !empty(APP_REGISTRATION_URL) && $checkDomainHttps) { ?>
        <div id="Register" class="tabcontent">
            <h3 class='mt-3 mb-3'><?php echo xlt('Register')?></h3>
            <div id="step_one">
                <p><?php echo xlt('Create an account to receive an API key') ?>.<?php echo xlt('Email address will be used for billing $48/mo payment') ?>.</p>
            <?php
            echo "<h4 class='mt-3'>" . xlt('Step 1 of 3') . ":</h4>";
            echo "<table class='table w-40'>";
            echo "<tr><td>" . xlt('Name') . ":</td><td><input type='text' name='fullname' size='25' value='' placeholder='". attr('Full Name') . "'></td></tr>";
            echo "<tr><td>" . xlt('Email') . ":</td><td><input type='text' name='email' size='25' value='' autocomplete='off'></td></tr>";
            echo "<tr><td>" . xlt('Password') . ":</td><td><input type='password' name='password' size='25' value='' autocomplete='off'></td></tr>";
            echo "<tr><td>" . xlt('Confirm Password') . ":</td><td><input type='password' name='confirm_password' value='' size='25' autocomplete='off'></td></tr>";
            echo "</table>";
            echo "<button id='create_account' class='btn btn-primary ml-3 mt-4'>" . xlt('Create Account') . "</button>";
            ?>
            </div>
            <div id="api-key"></div>
            <div id="step_two">
            <?php
            echo "<h4 class='mt-3'>" . xlt('Step 2 of 3') . ":</h4>";

                $uuid = UniqueInstallationUuid::getUniqueInstallationUuid();
                $facility = new FacilityService();
                $facilityInfo = $facility->getPrimaryBusinessEntity();
                if (
                    empty($facilityInfo['name']) ?? null ||
                    empty($facilityInfo['street']) ?? null  ||
                    empty($facilityInfo['city']) ?? null  ||
                    empty($facilityInfo['state'] ?? null ) ||
                    empty($facilityInfo['postal_code']) ?? null  ||
                    empty($facilityInfo['phone'] ?? null )
                ) {
                    echo "<p>" . xlt('Cannot register without a facility. Go to Admin->Config->Clinic->Facilities and create one and set as primary business entity') . "</p>";
                    echo "<p>" . xlt('Please fill in all fields, name, street, city, state, zip, and phone') . ".</p>";
                    echo "</div>";
                } else {
                    echo "<p>" . xlt('We need to collect some information from your installation. Below is the information that will be stored on our server') . ".</p>
                         <p>" . xlt('This information allows patients to connect with your installation of this program') . ".</p>";
            ?>
                <table class="table w-40">
                    <input type="hidden" name="api_key" value="<?php echo text($keyValue['api_key']) ?>">
                    <input type="hidden" name="registration_url" value="<?php echo text(APP_REGISTRATION_URL) ?>">
                    <input type="hidden" name="site_id" value="<?php echo text($_SESSION['site_id']) ?>">
                    <input type="hidden" name="foreign_id" value="<?php echo text($keyValue['remote_id']) ?>">
                    <tr>
                        <td><?php echo xlt('Unique Installation UUID') ?>:</td>
                        <td><input type='text' name='uuid' size='25' value='<?php echo text($uuid) ?>' readonly></td>
                    </tr>
                    <tr>
                        <td><?php echo xlt('Business Name') ?>:</td>
                        <td><input type='text' name='clinic_name' size='25' value='<?php echo text($facilityInfo['name']) ?>' readonly></td>
                    </tr>
                    <tr>
                        <td><?php echo xlt('Street') ?>:</td>
                        <td><input type='text' name='address' size='25' value='<?php echo text($facilityInfo['street']) ?>' readonly></td>
                    </tr>
                    <tr>
                        <td><?php echo xlt('City') ?>:</td>
                        <td><input type='text' name='city' size='25' value='<?php echo text($facilityInfo['city']) ?>' readonly></td>
                    </tr>
                    <tr>
                        <td><?php echo xlt('State') ?>:</td>
                        <td><input type='text' name='state' size='25' value='<?php echo text($facilityInfo['state']) ?>' readonly></td>
                    </tr>
                    <tr>
                        <td><?php echo xlt('Zip') ?>:</td>
                        <td><input type='text' name='zip' size='25' value='<?php echo text($facilityInfo['postal_code']) ?>' readonly></td>
                    </tr>
                    <tr>
                        <td><?php echo xlt('Phone') ?>:</td>
                        <td><input type='text' name='phone' size='25' value='<?php echo text($facilityInfo['phone']) ?>' readonly></td>
                    </tr>
                </table>
                <button id='registerBusiness' class='btn btn-primary ml-4 mt-2 mb-5'><?php echo xlt('Register Clinic') ?></button>
            </div>
            <?php } ?>
            <div id="step_three">
                <h4 class='mt-3'><?php echo xlt('Step 3 of 3') ?>:</h4>
                <div class="ml-5">
                    <p><?php echo xlt('You have successfully registered your clinic') ?>.</p>
                    <p><strong><?php echo xlt('Pay for Subscription') ?>:</strong></p>
                    <p><?php echo xlt('Your credit card information will be processed by Stripe. We do not store any card information') ?>:</p>
                    <h3 class="text-danger"><?php echo xlt('Use the same email address and phone number') . "<br>" . xlt('in the payment window so  we can match your payment') ?>:</h3>
                    <script async
                            src="https://js.stripe.com/v3/buy-button.js">
                    </script>

                    <stripe-buy-button
                        buy-button-id="buy_btn_1PHl9qGznLM7QeknR5uzbfIR"
                        publishable-key="pk_live_51J5VXMGznLM7QeknnHKskU4pEZ2Nqpe37p5sOG91CNCeMLxhJXmCS47RwCI15yS1NtxmGx0P5l5PQnABnkp4bRO1009VCrAup9"
                    >
                    </stripe-buy-button>
                </div>
            </div>
            <div id="reg_complete">
                <h4 class='mt-3'><?php echo xlt('Registration Complete') ?>:</h4>
                <p><?php echo xlt("A link will be provided on the patient portal page to install mobile app"); ?></p>
            </div>
        </div>
    <?php
    } else { ?>
        <div id="Register" class="tabcontent">
            <h3><?php echo xlt('Register')?></h3>
            <p><?php echo xlt('Cannot register without a public IP address, domain, and HTTPS connection') ?>.</p>
            <p><?php echo xlt("Your domain: https://") . APP_REGISTRATION_URL . xlt("/interface/smart/register-app.php") ?>
                <?php echo xlt("has to be reachable from the internet. Fix it") ?>!</p>
            <p><?php echo xlt("After clicking the link above if you see Not Authorized click the settings tab for instruction. "); ?></p>
        </div>
    <?php } ?>
        <div id="Settings" class="tabcontent">
            <h3><?php echo xlt('Settings')?></h3>
            <?php
            $fhir_settings = $GLOBALS['rest_fhir_api'];
            if ($fhir_settings) {
                echo "<p>" . xlt('REST FHIR API is enabled') . "</p>";
            } else {
                echo "<p>" . xlt("REST FHIR API is disabled") . "</p>";
                echo "<p>" . xlt("To enable REST FHIR API, go to Administration->Config->Connectors and enable it") . "</p>";
            }
            ?>
        </div>

        <div id="Contact" class="tabcontent">
            <h3><?php echo xlt('Contact') ?></h3>
            <p>support@ehrcommunityhelpdesk.com</p>
        </div>
    <script>
        function setVisibility(stepOne, stepTwo, stepThree, regComplete) {
            document.getElementById("step_one").hidden = stepOne;
            document.getElementById("step_two").hidden = stepTwo;
            document.getElementById("step_three").hidden = stepThree;
            document.getElementById("reg_complete").hidden = regComplete;
        }

        <?php if (empty($keyValue['api_key']) && empty($keyValue['remote_id'])) { ?>
        setVisibility(false, true, true, true);
        <?php } elseif (!empty($keyValue['api_key']) && empty($keyValue['remote_id'])) { ?>
        setVisibility(true, false, true, true);
        <?php } elseif (!empty($keyValue['api_key']) && !empty($keyValue['remote_id']) && $status['paid_status'] != 1) { ?>
        setVisibility(true, true, false, true);
        <?php } elseif ($status['paid_status'] == 1) { ?>
        setVisibility(true, true, true, false);
        <?php } ?>
    </script>
    <script src="js/script.js"></script>
    <script src="js/registerAccount.js"></script>
    <script src="js/registerBusiness.js"></script>

</body>
</html>

