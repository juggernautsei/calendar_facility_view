<?php

global $srcdir;

/*
 *   package   OpenEMR
 *   link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c)
 *  All rights reserved
 *
 */

global $include_root;

require_once dirname(__FILE__, 5) . "/globals.php";
require_once("$srcdir/patient.inc.php");
require_once("$srcdir/user.inc.php");

use Juggernaut\Dexcom\Module\DexconDataRequest;
use OpenEMR\Core\Header;
use OpenEMR\Menu\PatientMenuRole;
use OpenEMR\OeUI\OemrUI;
use Juggernaut\Dexcom\Module\DexcomTokenExchange;
use Juggernaut\Dexcom\Module\DexcomStoreRefreshToken;

$getNewAccessToken = new DexcomTokenExchange(); //Get the current access token exchanged for a new one
$newToken = $getNewAccessToken->getNewToken(); //returned values are stored in $newToken

if (!$newToken) {
    echo "<title>" . xlt('Dexcom Readings Access') . "</title>";
        echo "<span style='color: red; padding-top: 45px; padding-left: 150px'><h2>" . xlt("No token on file. Patient has not granted access to Dexcom") . "</h2></span>";
        echo "<a href='/interface/patient_file/summary/demographics.php' style='padding-left: 300px; padding-top: 10px'><button>" . xlt("Back to Patient") . "</button></a>";
    die();
}
$grantedAccess = json_decode($newToken, true);

new DexcomStoreRefreshToken($grantedAccess['refresh_token']); //store the new refresh token

?>
<html>
<head>
    <?php Header::setupHeader();?>
    <title><?php echo xlt('Glucose Readings'); ?></title>
    <link href="../lib/c3/c3.css" rel="stylesheet">
    <script src="../lib/c3/d3.v5.min.js" charset="utf-8"></script>
    <script src="../lib/c3/c3.min.js"></script>
    <?php
    $arrOeUiSettings = array(
        'heading_title' => xl('Glucose Readings'),
        'include_patient_name' => true,
        'expandable' => true,
        'expandable_files' => array("external_data_patient_xpd", "stats_full_patient_xpd", "patient_ledger_patient_xpd"),//all file names need suffix _xpd
        'action' => "reveal",//conceal, reveal, search, reset, link or back
        'action_title' => "",
        'action_href' => "",//only for actions - reset, link or back
        'show_help_icon' => false,
        'help_file_name' => ""
    );
    $oemr_ui = new OemrUI($arrOeUiSettings);
    ?>
</head>
<body>
    <div id="container_div" class="<?php echo $oemr_ui->oeContainer();?> mt-3">
        <div class="row">
            <div class="col-sm-12">
                <?php
                    require_once dirname(__FILE__, 5) . "/patient_file/summary/dashboard_header.php";
                ?>
            </div>
        </div>
            <?php
            $list_id = "dexcom"; // to indicate nav item is active, count and give correct id
            // Collect the patient menu then build it
            $menuPatient = new PatientMenuRole();
            $menuPatient->displayHorizNavBarMenu();
            ?>
        <div class="row">
            <div class="col-sm-12 mt-5">
              <h3 class="text-center"><?php echo xlt('Yesterday glucose readings'); ?></h3>
            </div>
            <div class="col-sm-12 mt-5" id="chart">
                <!-- display the patient readings -->
            </div>
            <div class="col-sm-12 mt-5">
                <p class="text-center"><?php echo xlt('Glucose readings are every 5 minutes starting at 0:05 AM'); ?></p>
            </div>
        </div>
    </div><!--end of container div-->
    <?php $oemr_ui->oeBelowContainerDiv();?>
    <?php
        if (isset($grantedAccess['access_token'])) {
            $patientReading = new DexconDataRequest();
            $patientReading->accessToken = $grantedAccess;
        } else {
            echo "Error: " . $grantedAccess['error'];
            die;
        }
        $readings = $patientReading->patientReading();
        $decodeReadings = json_decode($readings, true);

    $displayReadings = "'Glucose Readings'";
    foreach ($decodeReadings['egvs'] as $reading) {
        $displayReadings .= "," . $reading['value'];
    }

    ?>
    <script>
       const chart = c3.generate({
            bindto: '#chart',
            data: {
                columns: [
                    //['data1', 30, 200, 100, 400, 150, 250],
                    [<?php echo $displayReadings; ?>]
                ]
            }
        });
        /*
        require.config({
            baseUrl: '/js',
            paths: {
                d3: "http://d3js.org/d3.v5.min"
            }
        });
        require(["d3", "c3"], function(d3, c3) {
            c3.generate({
                ...
            });
        });*/
    </script>
</body>
</html>
