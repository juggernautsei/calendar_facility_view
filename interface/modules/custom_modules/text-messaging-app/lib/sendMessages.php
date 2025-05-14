<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
/*
 * @package OpenEMR
 *
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.
 * @license "All rights reserved"
 */


$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;

require_once dirname(__DIR__, 4) . "/globals.php";
require_once 'sms_appointment.php';

error_log('Starting appt reminders ' . date('Y-m-d H:i:s'));

start_appt_reminders();

error_log('Ending appt reminders ' . date('Y-m-d H:i:s'));

