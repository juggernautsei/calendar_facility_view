<?php

/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

require_once dirname(__FILE__, 5) . '/globals.php';

use Juggernaut\Module\App\Services\RegistrationProcess;

$data = file_get_contents('php://input');

$registration = new RegistrationProcess($data);

$returnKey = $registration->getApiKey();

echo "success";

/**
 *
 *  Note to self:
 * We don't need to return the api key. The key can be stored right way with the data from the form post.
 * We only need to return that the data has been successfully stored or it failed.
 * the combined.json file has the json data from the form post and the api key. That data needs to
 * be converted to a php array and stored in the database.
 *
 */
