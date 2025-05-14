<?php

/*
 * @package openemr
 * @link https://www.open-emr.org
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright (c) 2023
 * @license All Rights Reserved
 */

require_once dirname(__FILE__, 4) . '/globals.php';

use OpenEMR\Core\Header;
use OpenEMR\Modules\Clover\Controller\CloverConnectController;
use OpenEMR\Modules\Clover\BootstrapService;

$module_config = 1;

$controller = new CloverConnectController();

if (isset($_POST['access_token'])) {
    $credentials = [
        'app_secret' => $_POST['access_token'],
        'app_id' => null,
        'raid' => $_POST['raid'] ?? null,
        'hook' => null
    ];
    $process = $controller->storeAppCredentials($credentials);
}
if (isset($_POST['raid']) && isset($_POST['app_secret'])) {
    $credentials = [
        'app_secret' => $_POST['app_secret'],
        'app_id' => $_POST['app_id'] ?? null,
        'raid' => $_POST['raid'],
        'hook' => $_POST['hook'] ?? null
    ];
    $process = $controller->storeAppCredentials($credentials);
}
$credentials = $controller->getAppCredentials();

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Settings'); ?></title>
    <?php Header::setupHeader() ?>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-6 mt-5">
            <?php
            if ($credentials['app_secret'] && isset($_POST['access_token'])) {
                print '<p class="text-success">' . xlt('Success') . '</p>';
            }
            ?>
            <h1><?php echo xlt('Clover Settings'); ?></h1>

            <?php
                //Step One: redirect to api to get clients merchant ID
                if (!$credentials['app_secret']) {
                    ?>
                    <p><?php echo xlt('To setup this module, you will need an access token') ?></p>
                    <p><?php echo xlt('Select login to get token') ?></p>

                    <a class="btn btn-primary btn-large mb-3" href="https://api.affordablecustomehr.com/clover" target="_blank">
                        <?php echo xlt('Login into Clover') ?>
                    </a>
                    <form method="post" action="setup.php">
                        <div class="form-group">
                            <label for="client_id"><?php echo xlt('Client ID'); ?></label>
                            <input type="text" class="form-control" id="client_id" name="client_id" autocomplete="Off" value="">
                        </div>
                        <div class="form-group">
                            <label for="api_key"><?php echo xlt("API Key"); ?></label>
                            <input type="password" class="form-control ml-2" name="api_key" id="api_key" autocomplete="Off" value="">
                        </div>
                        <div class="form-group">
                            <label for="api_secret"><?php echo xlt('API Secret'); ?></label>
                            <input type="password" class="form-control" id="api_secret" name="api_secret" autocomplete="Off" value="">
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary btn-large" type="submit" value="Submit">
                        </div>
                    </form>
            <?php
                } else {
            ?>
            <form action="setup.php" method="post">
                <div class="form-group">
                    <label for="app_secret"><?php echo xlt('Access Token'); ?></label>
                    <input type="password" class="form-control" id="app_secret" name="app_secret" autocomplete="Off" value="<?php echo $credentials['app_secret']; ?>">
                </div>
                <div class="form-group">
                    <label for="raid"><?php echo xlt('Raid'); ?></label>
                    <input type="password" class="form-control" id="raid" name="raid" autocomplete="Off" value="<?php echo $credentials['raid']; ?>">
                </div>
                <button type="submit" class="btn btn-primary"><?php echo xlt('Update'); ?></button>
            </form>
            <?php
            }
            ?>
        </div>
        <div class="col-6 mt-5">
            <h3 class="mt-3"><?php echo xlt('Instructions'); ?></h3>
            <p><?php echo xlt('Thanks for installing the Clover module. To enable Clover, you need to enter your Clover settings.'); ?></p>
            <p><?php echo xlt('1) Click the Login button to log into your clover account.'); ?></p>
            <p><?php echo xlt('2) Your access token and RAID key will be display in the new tab.'); ?></p>
            <p><?php echo xlt('3) Copy the access token and RAID key. Paste them in the boxes to the left.'); ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-5">
            <!--<h3 class="mt-3"><?php echo xlt('API Token Verification'); ?></h3>-->
        </div>
    </div>
</div>
</body>
</html>
