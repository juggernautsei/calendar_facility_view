<?php

namespace Juggernaut\Module\BulkEmail;
require_once dirname(__FILE__, 5) . '/globals.php';

use OpenEMR\Core\Header;
use Juggernaut\Module\BulkEmail\StoreMessages;
use Juggernaut\Module\BulkEmail\SystemData;

if (!empty($_POST)) {
    //die('Post complete - Module is not configured yet');
    require_once "../src/StoreMessages.php";
    $messageData = new StoreMessages();
    $messageData->subject = $_POST['subject'];
    $messageData->message = $_POST['message'];
    $messageData->date = date('Y-m-d H:i:s');
    $messageData->user = $_SESSION['authUser'];
    $addressCount = new SystemData();
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Email Communications') ?></title>
    <?php Header::setupHeader(); ?>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12 mt-5">
            <h1><?php echo xlt('Email Communications') ?></h1>
            <p><?php echo xlt('Send mass email to all patients that have an email address in their profile') ?></p>
            <p><?php echo xlt('Enter the message to send to patients') ?></p>
            <p><?php echo xlt('#Addresses') . $addressCount->countEmails(); ?></p>
            <form method="post" action="email.php" onsubmit="return isthisempty();">
                <input type="text" name="subject" id="subject" class="form-control mt-3 mb-3" placeholder="Subject">
                <textarea name="message" id="message" cols="30" rows="10" class="form-control"></textarea>
                <button type="submit" class="btn btn-primary mt-3"><?php echo xlt('Send') ?></button>
            </form>
        </div>
    </div>
</div>
</body>
    <script>
        function isthisempty() {
            if (document.getElementById('subject').value === '') {
                alert('Please enter a subject');
                return false;
            }
            if (document.getElementById('message').value === '') {
                alert('Please enter a message');
                return false;
            }
            return true;
        }
    </script>
</html>
