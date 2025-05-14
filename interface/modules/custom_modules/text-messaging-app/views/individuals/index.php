<?php

/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

use OpenEMR\Core\Header;

    // Validate the input phone number
    if (!isset($_GET['phone']) || !preg_match('/^\d{10}$/', $_GET['phone'])) {
        // Handle invalid phone number
        ?>
        <script>
            alert("Invalid phone number. Please enter a 10-digit phone number.");
            // Optionally, you can also redirect the user back to the previous page
            // window.history.back();
        </script>
        <?php
        exit;
    }

    // Get the phone number
    $phoneNumber = $_GET['phone'];

    // Generate the HTML page
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Send Text</title>
    <?php Header::setupHeader(['common']); ?>
</head>
<body>
<div class="container-fluid m-2 main_container">
    <h1><?php echo xlt('Send Text'); ?></h1>
    <p><?php echo xlt("To") . ": " . $phoneNumber; ?></p>
    <!-- Rest of the HTML code -->
    <p><?php
        $digits = strlen($_GET['phone']);
        if ($digits < 10) { echo "<span class='text-danger'>" . xlt('Invalid phone number') . "</span>"; }
        ?></p>
    <form id="text_form" action='../../public/index.php/texting/individualPatient' method="post">
        <input type="hidden" name="phone" value="<?php echo trim($_GET['phone']); ?>">
        <textarea class="form-control col-6 mb-2" name="messageoutbound"></textarea>
        <button id="my-form-button" class="form-control col-2 mb-2"><?php echo xlt('Send'); ?></button>
    </form>
    <p id="my-form-status"></p>
</div>
<script>
    const form = document.getElementById("text_form");

    async function handleSubmit(event) {
        event.preventDefault();
        const status = document.getElementById("my-form-status");
        const data = new FormData(event.target);

        try {
            const response = await fetch(event.target.action, {
                method: form.method,
                body: data,
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    status.innerHTML = `Message delivered to patient. Message ID: ${result.textId}. Remaining credits ${result.quotaRemaining}`;
                } else {
                    status.innerHTML = '<?php echo xlt('Not delivered. Possibly invalid cell number'); ?>';
                }
            } else {
                const errorData = await response.json();
                status.innerHTML = errorData.errors.map(error => error.message).join(', ');
            }
        } catch (error) {
            status.innerHTML = '<?php echo xlt("Oops! There was a problem submitting your form"); ?>';
        }

        form.reset();
    }
    form.addEventListener("submit", handleSubmit)
</script>

</body>
<div class="p-5">
    <i><?php echo xlt('Juggernaut Systems Express'); ?></i> &copy; 2020 <?php  echo " - " . date('Y'); ?>
</div>
</html>
