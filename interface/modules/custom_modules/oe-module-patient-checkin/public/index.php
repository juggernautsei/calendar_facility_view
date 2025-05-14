<?php

//prevent UI redressing
Header("X-Frame-Options: DENY");
Header("Content-Security-Policy: frame-ancestors 'none'");

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once dirname(__DIR__, 4) . "/globals.php";
require_once '../src/Services/PatientCheck.php';

// Auth if core or portal.
require_once dirname(__DIR__, 5) . "/src/Common/Session/SessionUtil.php";
OpenEMR\Common\Session\SessionUtil::portalSessionStart();

use OpenEMR\Services\FacilityService;
use Juggernaut\Module\PatientCheckin\PatientCheck;
use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;

$facilityService = new FacilityService();
$patient = new PatientCheck();
$locationName = $facilityService->getById(3);
$locations = $facilityService->getAllFacility();

//see portal index page for example
CsrfUtils::setupCsrfKey();

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = htmlspecialchars($_POST['patient_name']);
    $postdob = htmlspecialchars($_POST['patient_dob']);

    $existingData = $patient->checkIn($name, $postdob);
    if(!empty($existingData['message'])) {
        $message = $existingData['message'];
    } else {
        $encodedData = urlencode(serialize($existingData));
        header("Location: ../view/patient_data_page.php?data=$encodedData");
        exit();
    }
}
$logoPath =  "../../../../../sites/default/images/logos/core/login/primary/logo.png";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Patient Checkin'); ?></title>
    <?php Header::setupHeader(['common']);?>

    <style>

    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-12">
            <?php
            if (file_exists($logoPath)) {
                echo "<img src='$logoPath' class='img-fluid mx-auto d-block' alt='logo'>";
            } ?>
            <h3 class="text-center mt-5"><?php echo xlt($locationName['name']); ?></h3>
            <p class="text-center"><strong><?php echo xlt('Check-in'); ?></strong></p>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mx-auto">
            <p class="text-danger" id="error-message"><?php echo xlt($message); ?></p>
            <form name="my_form" method="post" action="index.php" onsubmit="return top.restoreSession()">
                <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken('autologin')); ?>" />
                <div class="form-group">
                    <label for="patient_name" class="font-weight-bold"><?php echo xlt('Patient Name'); ?></label>
                    <input type="text" class="form-control" id="patient_name" name="patient_name" placeholder="Jane Doe">
                </div>
                <div class="form-group">
                    <label for="patient_name" class="font-weight-bold"><?php echo xlt('DOB'); ?></label>
                    <input type="text" class="form-control" id="patient_dob" name="patient_dob" placeholder="mm-dd-yyyy">
                </div>
                <div class="form-group">
                    <label for="facility" class="font-weight-bold"><?php echo xlt('Facility'); ?></label>
                    <select class="form-control" id="facility" name="facility">
                        <?php
                        //Make this a foreach loop
                        foreach ($locations as $location) {
                            echo "<option value='{$location['id']}'>{$location['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary" value="<?php echo xlt('Checkin'); ?>">
                </div>
                <div class="mt-5">
                    <p class="text-center"><?php include_once "hipaa.php"?></p>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
<script>
    $(document).ready(function() {
        $('#patient_dob').on('input', function() {
            var date = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(date.replace(/(\d{2})(\d{2})/, '$1-$2-'));
        });
    });
</script>
<script src="../js/common.js"></script>
</html>
