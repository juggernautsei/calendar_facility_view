<?php

/*
 *   @package   OpenEMR
 *   @link      http://www.open-emr.org
 *
 *   @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *   Copyright (c)  Juggernaut Systems Express
 *   @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

require_once dirname(__DIR__, 3) . '/globals.php';
//require_once dirname(__FILE__) . '/public/monthly_statements.php';

use OpenEMR\Core\Header;
use OpenEMR\Common\Csrf\CsrfUtils;
use Juggernaut\Module\PatientStatements\Services\ProcessOutboundPlaceholders;

$process = new ProcessOutboundPlaceholders();

if (!empty($_POST['csrf_token']) && CsrfUtils::verifyCsrfToken($_POST['csrf_token'])) {
    $message = $_POST['outbound_message'];
    $process->saveMessage($message);
}

$statement = $process->getStoredMessage();
$nsm = xlt('No saved message');

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Statements</title>
    <?php Header::setupHeader(); ?>
</head>
<body>
<div class="container">
    <h1 class="text-center"><?php echo xlt('Monthly Statements') ?></h1>
    <div class="row">
        <div class="col-md-12 mt-3">
            <p><?php echo xlt("This process will only be run on the first day of the month") ?></p>
            <?php
                //sendPatientBalance();
            ?>
            <form action="setup.php" method="post">
                <div class="form-group">
                    <input type="hidden" name="csrf_token" value="<?php echo CsrfUtils::collectCsrfToken(); ?>">
                    <label for="outbound_message"><?php echo xlt('Outbound Message Template') ?>
                        <textarea type="text" maxlength="180" class="form-control" id="outbound_message" name="outbound_message" rows="8" cols="80"><?php echo !empty($statement) ? $statement : $nsm ?></textarea>
                    </label>
                </div>
                <span id="charCount"><?php echo xlt("180 characters remaining") ?></span><br>
                <button type="submit" class="btn btn-primary"><?php echo xlt('Save Message') ?></button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mt-3">
            <h3><?php echo xlt('Sample outbound message') ?></h3>
            <p><b><?php echo xlt('Placeholders: ') ?></b><?php echo xlt('{balance} {clinic} {dos} not all placeholders have to be used') ?></p>
            <p><?php echo xlt("You have a balance of ")?>
                <b>{<?php echo xlt("balance") ?>}</b> <?php echo xlt("with") ?>
                <b>{<?php echo xlt("clinic") ?>}</b>
                <?php echo xlt("for your care at PRMC on") ?> <b>{<?php echo xlt("dos") ?>}</b>.
                <?php echo xlt("If you would like a link to your patient portal call") ?></p>
        </div>
    </div>
</div>
<script>
    const textarea = document.getElementById('outbound_message');
    const charCount = document.getElementById('charCount');
    const maxChars = 180;

    textarea.addEventListener('input', () => {
        const remaining = maxChars - textarea.value.length;
        charCount.textContent = `${remaining} characters remaining`;

        // If characters exceed the limit, truncate the text
        if (remaining < 0) {
            textarea.value = textarea.value.substring(0, maxChars);
        }
    });
</script>
</body>
</html>
