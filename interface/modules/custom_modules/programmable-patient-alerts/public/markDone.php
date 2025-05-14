<?php

/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 * Copyright (c) 2023.
 */

use Juggernaut\Module\PatientAlerts\AlertsManager;
use OpenEMR\Common\Csrf\CsrfUtils;

require_once dirname(__FILE__, 5) . '/globals.php';

$module_config = 1;

$updateAlert = new AlertsManager();

if ($_POST['id'] ?? null) {
    if (!CsrfUtils::verifyCsrfToken($_POST['csrf_token_form'])) {
        echo "<script>alert('Invalid CSRF token. Please try again.')</script>";
        die;
    }
    $updateAlert->alertId = $_POST['id'];
    $response = $updateAlert->markDone();
    if (!$response) {
        echo "<script>alert('Alert was not stored. Please try again.')</script>";
        error_log($response);
    }
}
