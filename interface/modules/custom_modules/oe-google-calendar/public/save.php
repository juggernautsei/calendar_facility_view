<?php

/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

require_once dirname(__DIR__, 4) . '/globals.php';

use OpenEMR\Common\Csrf\CsrfUtils;
use Juggernaut\OpenEMR\Modules\GoogleCalendar\Services\CredentialService;

if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
    CsrfUtils::csrfNotVerified();
}

if (!empty($_POST['client_id']) && !empty($_POST['client_secret']) && !empty($_POST['redirect_uri'])) {
    //sanitize the input
    $client_id = filter_input(INPUT_POST, 'client_id', FILTER_SANITIZE_SPECIAL_CHARS);
    $client_secret = filter_input(INPUT_POST, 'client_secret', FILTER_SANITIZE_SPECIAL_CHARS);
    $redirect_uri = filter_input(INPUT_POST, 'redirect_uri', FILTER_SANITIZE_SPECIAL_CHARS);

    $credential = new CredentialService();
    $credential->client_id = $client_id;
    $credential->client_secret = $client_secret;
    $credential->redirect_uri = $redirect_uri;
    $status = $credential->saveCredential();

    echo "Credentials saved ";
} else {
    header('Location: index.php');
}

