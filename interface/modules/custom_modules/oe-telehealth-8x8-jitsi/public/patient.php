<?php

/*
 * @package OpenEMR
 *
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * @license "All rights reserved"
 */

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
require_once dirname(__FILE__, 5) . '/globals.php';
require_once dirname(__FILE__, 2) . '/vendor/autoload.php';
require_once("$srcdir/patient.inc");

use Juggernaut\Modules\Jitsi8x8Telehealth\Jitsi8x8Telehealth\Jitsi8x8Telehealth\Jitsi8x8Telehealth\JavaWebToken;
use Juggernaut\Modules\Jitsi8x8Telehealth\Jitsi8x8Telehealth\Jitsi8x8Telehealth\Jitsi8x8Telehealth\BuildRoom;

$room = $_GET['q'];
$pid = $_GET['c'];
$patient = new BuildRoom();
$token = new JavaWebToken();
$details = $patient->getPatientInfo($pid);

$username = "Enter Name Here";

?>

<!DOCTYPE html>
<html>
<head>
    <script src='https://8x8.vc/vpaas-magic-cookie-02bc0019d5a3438186239dc1711e0ee1/external_api.js' async></script>
    <style>html, body, #jaas-container { height: 100%; }</style>
    <script type="text/javascript">
        window.onload = () => {
            const api = new JitsiMeetExternalAPI("8x8.vc", {
                roomName: "vpaas-magic-cookie-02bc0019d5a3438186239dc1711e0ee1/<?php echo $room ?>",
                parentNode: document.querySelector('#jaas-container'),
                jwt: "<?php echo $token->buildToken($details['email'], $username, false, $room) ?>"
            });
        }
    </script>
</head>
<body><div id="jaas-container" /></body>
</html>
