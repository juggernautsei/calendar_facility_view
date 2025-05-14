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

$status = $registration->pushBusinessData();

header('Content-Type: application/json');
echo $status;

