<?php


/*
 * @package OpenEMR
 *
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * @license "All rights reserved"
 */

$ignoreAuth = true;
require_once dirname(__FILE__, 5) . '/globals.php';
require_once dirname(__FILE__, 2) . '/vendor/autoload.php';

use Juggernaut\Modules\Jitsi8x8Telehealth\Jitsi8x8Telehealth\Jitsi8x8Telehealth\Jitsi8x8Telehealth\JavaWebToken;
use Juggernaut\Modules\Jitsi8x8Telehealth\Jitsi8x8Telehealth\Jitsi8x8Telehealth\Jitsi8x8Telehealth\BuildProviderProfile;
use Juggernaut\Modules\Jitsi8x8Telehealth\Jitsi8x8Telehealth\Jitsi8x8Telehealth\Jitsi8x8Telehealth\BuildRoom;
use Juggernaut\Modules\Jitsi8x8Telehealth\Jitsi8x8Telehealth\Jitsi8x8Telehealth\Jitsi8x8Telehealth\TextPatientMeetingLink;

$room = new BuildRoom();
//$roomValue = $room->uniquePatientRoom();


$token = new JavaWebToken();
$details = [];
if (!empty($_SESSION['authUserID'])) {
    $p_room = $room->createMeetingId();
    $fetchProvider = new BuildProviderProfile();
    $details = $fetchProvider->providerFetchDetails();
    $ismoderator = true;
    if (!is_array($details)) {
        die("Something went wrong with provider add email address in the address book");
    } elseif (empty($details['email'])) {
        die("Provider email is missing!");
    }
} elseif (!empty($_GET['moderator'])) {
    $ismoderator = true;
    $details['email'] = $_POST['email'];
    $details['fname'] = $_POST['fname'];
    $details['lname'] = $_POST['lname'];
    $p_room = $_POST['cellphone'];
} else {
    die("Something went wrong with provider add email address in the address book");
}

$username = $details['fname'] . " " . $details['lname'];
$email = $details['email'];

if (!empty($_POST['cellphone'])) {
    echo text("Sending text message to patient");
    $contactPatient = new TextPatientMeetingLink($_POST['cellphone']);
    $contactPatient->textLink();
}
?>

<!DOCTYPE html>
<html>
<head>
    <script src='https://8x8.vc/vpaas-magic-cookie-02bc0019d5a3438186239dc1711e0ee1/external_api.js' async></script>
    <style>html, body, #jaas-container { height: 100%; }</style>
    <script type="text/javascript">
        window.onload = () => {
            const api = new JitsiMeetExternalAPI("8x8.vc", {
                roomName: "vpaas-magic-cookie-02bc0019d5a3438186239dc1711e0ee1/<?php echo $p_room ?>",
                parentNode: document.querySelector('#jaas-container'),
                jwt: "<?php echo $token->buildToken($details['email'], $username, $ismoderator, $p_room) ?>"
            });
        }
    </script>
</head>
<body><div id="jaas-container" /></body>
</html>

