<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

namespace Juggernaut\Text\Module\App;

require_once dirname(__DIR__, 4) . '/globals.php';

use OpenEMR\Core\Header;

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt("Set Outbound Message"); ?></title>
    <?php Header::setupHeader(['common']); ?>
    <style>
        .char-counter {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            transition: color 0.3s ease;
        }
        .char-counter.limit-reached {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center"><?php echo xlt('Setting Center') ?></h1>
    <?php require_once dirname(__DIR__) . "/views/nav_top.php"; ?>
    <div class="row">
        <div class="col-md-6">
            <h1 class="text-center"><?php echo xlt('Set Outbound Message') ?></h1>
            <form action="/index.php/message/appointment" method="post">
                <div class="form-group">
                    <label for="message"><?php echo xlt('Appointment Reminder Message'); ?></label>
                    <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                    <div id="charCounter" class="char-counter">Characters: 0 / 180</div>
                    <button type="submit" class="btn btn-primary mt-3"><?php echo xlt('Save'); ?></button>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <h3><?php echo xlt('Instructions') ?></h3>
            <p><?php echo xlt('This message will be sent to patients as a reminder of their upcoming appointment.') ?></p>
            <p><?php echo xlt('The message should be concise and informative, and should not exceed 180 characters.') ?></p>
            <p><?php echo xlt('The following placeholders can be used in the message:') ?></p>
            <ul>
                <li><?php echo xlt('Patient Name: {patient_name}') ?></li>
                <li><?php echo xlt('Appointment Date: {appointment_date}') ?></li>
                <li><?php echo xlt('Appointment Time: {appointment_time}') ?></li>
                <li><?php echo xlt('Facility Name: {facility_name}') ?></li>
                <li><?php echo xlt('Facility Address: {facility_address}') ?></li>
                <li><?php echo xlt('Facility Phone: {facility_phone}') ?></li>
            </ul>
            <!-- Write a sample message here -->
            <h3><?php echo xlt('Sample Message:') ?></h3>
            <p><?php echo xlt('Hello {patient_name}, your appointment is scheduled for {appointment_date} at {appointment_time}.') ?></p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('message');
        const counter = document.getElementById('charCounter');
        const CHAR_LIMIT = 180;

        // Function to update character count
        function updateCharCount() {
            const count = textarea.value.length;
            counter.textContent = `Characters: ${count} / ${CHAR_LIMIT}`;

            // Add or remove the limit-reached class based on character count
            if (count >= CHAR_LIMIT) {
                counter.classList.add('limit-reached');
                // Optional: Truncate text to 180 characters
                if (count > CHAR_LIMIT) {
                    textarea.value = textarea.value.substring(0, CHAR_LIMIT);
                }
            } else {
                counter.classList.remove('limit-reached');
            }
        }

        // Add event listeners for real-time counting
        textarea.addEventListener('input', updateCharCount);
        textarea.addEventListener('keyup', updateCharCount);
        textarea.addEventListener('paste', function() {
            // Use setTimeout to ensure we get the pasted content
            setTimeout(updateCharCount, 0);
        });
    });
</script>
</body>
</html>
