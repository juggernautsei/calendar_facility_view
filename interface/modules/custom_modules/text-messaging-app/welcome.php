<?php

/**
 *   package   OpenEMR
 *   link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c)
 *  All rights reserved
 *
 */

require_once dirname(__FILE__, 4) . "/globals.php";

use Juggernaut\Text\Module\App\Model\SettingModel;
use OpenEMR\Core\Header;
use Juggernaut\Text\Module\App\Model\NotificationModel;
use OpenEMR\Common\Csrf\CsrfUtils;
use Juggernaut\Text\Module\App\Controllers\Credentials;

$data = new NotificationModel();
$apptstatuses = new SettingModel();
$credentials = new Credentials();

if (!empty($_POST['token'])) {
    if (!CsrfUtils::verifyCsrfToken($_POST["token"])) {
        CsrfUtils::csrfNotVerified();
    }

    if ((int)$_POST['enableSms'] == 1) {
        echo $apptstatuses->enableSmsServices();
    } elseif ((int)$_POST['enableSms'] == 0) {
        echo $apptstatuses->disableSmsServices();
    }

}

$activeStatus =  $apptstatuses->statusOfSmsService();
$savedCredentials = $credentials->getCredentials();

if (!empty($savedCredentials)) {
    $credentialStatus = "<p style='color: green'>" . xlt('Credentials Saved') . "</p>";
} else {
    $credentialStatus = "<p style='color: red'>" . xlt('Credentials Not Saved') . "</p>";
}
$twilioNumber = json_decode($savedCredentials['settings'], true);

function IsValidFQDN(string $FQDN): bool
{
    return (bool) filter_var(gethostbyname($FQDN), FILTER_VALIDATE_IP);
}

?>
    <!doctype html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title><?php echo xlt('Settings'); ?></title>
        <?php Header::setupHeader(['common']); ?>
    </head>
    <body>
    <div class="container mt-5">
        <h1 class="text-center"><?php echo xlt('Welcome to Two-Way Text Messaging'); ?></h1>
        <div class="row">
            <div class="col-md-12 mx-auto" style="width: 80%">
                <h1 class="text-center"><?php echo xlt('Setting Center') ?></h1>
                <?php require_once dirname(__DIR__) . "/text-messaging-app/views/nav_top.php"; ?>
                <div class="m-3">
                    <h3><?php echo xlt('Enable and Disable Text Notifications'); ?></h3>
                    <p><?php echo xlt('This button turns on an off the SMS appointment notifications being sent to patients'); ?></p>
                    <?php if ($activeStatus['active'] == 0) { ?>
                        <form class="" method="post" action="settings.php" name="enableSMSService">
                            <input type="hidden" name="enableSms" value="1">
                            <input type="hidden" name="token" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>">
                            <input type="submit" class="btn btn-success" value="Enable Notifications" title="This will enable the sending SMS messages">
                        </form>
                    <?php } else { ?>
                        <form class="" method="post" action="settings.php" name="enableSMSService">
                            <input type="hidden" name="disableSms" value="0">
                            <input type="hidden" name="token" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>">
                            <input type="submit" class="btn btn-danger" value="Disable Notifications" title="This will stop the sending of SMS messages">
                        </form>
                    <?php } ?>
                </div>
                <div class="m-2">
                    <h3><?php echo xlt('Settings for SMS Notifications'); ?></h3>
                    <strong><?php echo "The SMS notifications will be sent at " .$GLOBALS['SMS_NOTIFICATION_HOUR'] . " "
                            . xlt(' hours in advanced of appointment, send notification') . "<br>"
                            . xlt('This setting is in Configuration') ?></strong><br><br>
                    <span style="color: red;">
                    <?php
                    $timezone = $data->getTimeZoneInfo();
                    if($timezone['gl_value'] == '') {
                        echo $GLOBALS['gbl_time_zone'] . xlt('The time zone needs to be set for messages to go out at the right time');
                    }
                    ?>
                    </span>
                </div>
                <div class="method">
                    <?php
                    $FQDN = $_SERVER['HTTP_HOST'];
                    $hasSubDomain = substr_count($FQDN, ".") . "<br>";
                    $domainNameRoot = '';
                    if ($hasSubDomain > 1) {
                        $cutLocation = strpos($FQDN, ".") + 1 . "<br>";
                        $domainNameRoot = substr($FQDN, (int)$cutLocation);
                    } else {
                        $domainNameRoot = $_SERVER['HTTP_HOST'];
                    }

                    if (IsValidFQDN($domainNameRoot) != 1) {
                        echo "<span style='font-weight: bold; color: red'>" . xlt('You have to have a fully qualified domain name to use this module to receive inbound text') . "</span><br>";
                        echo xlt("i.e. https://www.clinic-on-the-corner.com") . "<br><br>";
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php if (IsValidFQDN($domainNameRoot) == 1) { ?>
        <div class="row">
            <div class="col-md-6">
                <h3><?php echo xlt('SMS Credentials'); ?></h3>
                <form class="form" method="post" action="public/index.php/credentials" name="smsSettings" autoComplete="off">
                    <input type="hidden" name="token" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>">
                    <div class="form-group">
                        <?php echo $credentialStatus ?>
                        <label for="smsApiKey"><?php echo xlt('Account Sid'); ?></label>
                        <input type="password" class="form-control" id="smsApiKey" name="accountSid" value="">
                    </div>
                    <div class="form-group">
                        <label for="smsApiKey"><?php echo xlt('SMS API Key'); ?></label>
                        <input type="password" class="form-control" id="smsApiKey" name="smsApiKey" value="">
                    </div>
                    <div class="form-group">
                        <label for="smsnumber"><?php echo xlt('SMS Number'); ?></label>
                        <input type="text" class="form-control" id="smsnumber" name="smsnumber" value="<?php echo $twilioNumber['twilioNumber'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="messagingServiceSid"><?php echo xlt('Service Sid'); ?></label>
                        <input type="text" class="form-control" id="messagingServiceSid" name="messagingServiceSid" value="">
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="<?php echo xlt('Save'); ?>">
                    </div>
                </form>
            </div>
        </div>
        <?php } ?>
    </div>
    <script src="lib/js/nav.js"></script>
    </body>
    <div class="p-5">
    <i><?php echo xlt('Juggernaut Systems Express'); ?></i> &copy; 2020 <?php  echo " - " . date('Y'); ?>
    </div>
    </html>
