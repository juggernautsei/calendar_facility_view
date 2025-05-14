<?php

/*
 *
 * @package OpenEMR
 * @link    https://www.open-emr.org
 * author Sherwin Gaddis <sherwingaddis@gmail.com>
 * All Rights Reserved
 * @copyright Copyright (c) 2024.
 */

require_once dirname(__DIR__, 3) . '/globals.php';

use OpenEMR\Core\Header;
use Juggernaut\Module\HealthScribe\Controllers\AwsCredentialManager;

$awsCredentialManager = new AwsCredentialManager();
$credentialEntered = $awsCredentialManager->areCredentialsSaved();
$status = $_GET['error'];

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('AWS Health Scribe Settings') ?></title>
    <?php Header::setupHeader(); ?>
</head>
<body>
<div class="container">
    <?php if ($status == 'error') { ?>
        <div class="alert alert-danger" role="alert">
            <?php echo xlt('Error: Invalid AWS credentials'); ?>
        </div>
    <?php } ?>
    <h1><?php echo xlt('AWS Health Scribe Settings') ?></h1>
    <div class="row">
        <?php if (!$credentialEntered) { ?>
        <div class="col-md-6">
            <p>
                <?php echo xlt('This module allows you to send patient data to AWS Health Scribe.') ?>
            </p>
            <form method="post" action="save_settings.php">
                <div class="form-group">
                    <label class="label" for="access_key">AWS Access Key</label>
                    <input class="form-control" type="text" id="access_key" name="access_key" required>
                </div>
                <div class="form-group">
                    <label class="label" for="secret_key">AWS Secret Key</label>
                    <input class="form-control" type="password" id="secret_key" name="secret_key" required>
                </div>
                <div class="form-group">
                    <label class="form-select-label" for="region"><?php echo xlt('AWS Region') ?></label>
                    <select class="form-control" id="region" name="region">
                        <option value="us-west-1"><?php echo xlt('US West (N. California)') ?></option>
                        <option value="us-west-2"><?php echo xlt('US West (Oregon)') ?></option>
                        <option value="us-east-1"><?php echo xlt('US East (N. Virginia)') ?></option>
                        <option value="us-east-2"><?php echo xlt('US East (Ohio)') ?></option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo xlt('Save') ?></button>
            </form>
        </div>
        <?php } else { ?>
        <div class="col-md-12">
            <p><?php echo xlt("Credentials have been saved"); ?></p>
        </div>
        <?php } ?>
    </div>
</div>
</body>
</html>
