<?php

//prevent UI redressing
Header("X-Frame-Options: DENY");
Header("Content-Security-Policy: frame-ancestors 'none'");


$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once dirname(__DIR__, 4)."/globals.php";
require_once '../src/Services/PatientCheck.php';

use OpenEMR\Services\FacilityService;
use Juggernaut\Module\PatientCheckin\PatientCheck;
use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;

$facilityService = new FacilityService();
$patient = new PatientCheck();
$locationName = $facilityService->getById(3);
//see portal index page for example
CsrfUtils::setupCsrfKey();

$message = '';
$patientData = [];
$id = '';
if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $patientData = $patient->existingPatientInfo($id);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST;
    $existingData = $patient->updateInfo($data);

    if($existingData['message'])
    {
        $message = $existingData['message'];
    }
    else
    {
        header("Location: ../view/insurance.php?id=" . $existingData['id']);
        exit();
    }
}
var_dump($_SESSION['pid']);
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Patient Checkin'); ?></title>
    <?php Header::setupHeader(['common']); ?>
</head>
<body>
<div class="container">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h3 class="text-center mt-5"><?php echo xlt($locationName['name']); ?></h3>
            <p class="text-center"><strong><?php echo xlt('Update your profile data here'); ?></strong></p>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mx-auto">
            <p class="text-danger" id="error-message"><?php echo xlt($message); ?></p>
            <form name="my_form" method="post" action="patient_change_data.php" onsubmit="return top.restoreSession()">
                <input type="hidden" name="csrf_token_form"
                       value="<?php echo attr(CsrfUtils::collectCsrfToken('autologin')); ?>"/>
                <input type="hidden" name="id"
                       value="<?php echo $patientData['id'] ?>"/>
                <div class="form-group">
                    <label for="patient_name" class="font-weight-bold"><?php echo xlt('Patient Name'); ?></label>
                    <input type="text" class="form-control" id="patient_name"
                           value="<?php echo $patientData['fname'] ?> <?php echo $patientData['lname'] ?>"
                           name="patient_name" placeholder="Jane Doe">
                </div>
                <div class="form-group">
                    <label for="DOB" class="font-weight-bold"><?php echo xlt('DOB'); ?></label>
                    <input type="text" class="form-control" id="DOB" value="<?php echo date('m/d/Y', strtotime($patientData['DOB'])) ?>" name="DOB" placeholder="mm/dd/yyyy">
                </div>
                <div class="form-group">
                    <label for="phone_cell" class="font-weight-bold"><?php echo xlt('Phone number'); ?></label>
                    <input type="text" class="form-control" id="phone_cell" value="<?php echo $patientData['phone_cell'] ?>"
                           name="phone_cell" placeholder="phone number">
                </div>
                <div class="row">
                    <div class="form-group col-md-12 col-lg-6 col-sm-12">
                        <label for="street" class="font-weight-bold"><?php echo xlt('Street'); ?></label>
                        <input type="text" class="form-control" id="street"
                               value="<?php echo $patientData['street'] ?>" name="street" placeholder="street">
                    </div>
                    <div class="form-group col-md-12 col-lg-6 col-sm-12">
                        <label for="city" class="font-weight-bold"><?php echo xlt('City'); ?></label>
                        <input type="text" class="form-control" id="city"
                               value="<?php echo $patientData['city'] ?>" name="city" placeholder="city">
                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-md-12 col-lg-6 col-sm-12">
                        <label for="state" class="font-weight-bold"><?php echo xlt('State'); ?></label>
                        <input type="text" class="form-control" id="state"
                               value="<?php echo $patientData['state'] ?>" name="state" placeholder="state">
                    </div>
                    <div class="form-group col-md-12 col-lg-6 col-sm-12">
                        <label for="postal_code" class="font-weight-bold"><?php echo xlt('Postal Code'); ?></label>
                        <input type="text" class="form-control" id="postal_code"
                               value="<?php echo $patientData['postal_code'] ?>" name="postal_code" placeholder="postal code">
                    </div>
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary mt-2 w-50" value="<?php echo xlt('Update'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>
</body>
<script>
    $(document).ready(function (){
        let errorMessage = $("#error-message").text();
        if(errorMessage.length > 0){
            setTimeout(() => {
                $("#error-message").text('');
                window.location.href = "patient_change_data.php?id=<?php echo $id; ?>";
            }, 1500);
        }
    });
</script>
</html>
