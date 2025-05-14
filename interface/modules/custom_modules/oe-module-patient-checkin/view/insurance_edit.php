<?php

Header("X-Frame-Options: DENY");
Header("Content-Security-Policy: frame-ancestors 'none'");

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once dirname(__DIR__, 4)."/globals.php";
require_once '../src/Services/InsuranceCheck.php';

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
use Juggernaut\Module\PatientCheckin\InsuranceCheck;

CsrfUtils::setupCsrfKey();
$companies = [];
$insurance = new InsuranceCheck();
$message = '';

$id = '';
$companyID = '';

if (isset($_GET['pid']) && isset($_GET['companyId'])) {
    $id = $_GET['pid'];
    $companyID = $_GET['companyId'];
} else {
    header("Location: ../public/index.php");
    exit();
}
$insuranceData = $insurance->getInsuranceData($id, $companyID);
if(isset($insuranceData['message']))
{
    $message = $insuranceData['message'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST;
    $existingData = $insurance->updateInsurance($data);

    if($existingData['message'])
    {
        $message = $existingData['message'];
    }
    else
    {
        header("Location: ../view/payment.php?id=" . $id);
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
        <div class="col-12 ">
            <h3 class="text-center mt-5"><?php echo xlt('Update Insurance'); ?></h3>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mx-auto">
            <p class="text-danger" id="error-message"><?php echo xlt($message); ?></p>
            <form name="my_form" method="post" action="" onsubmit="return top.restoreSession()">
                <input type="hidden" name="csrf_token_form" id="csrf_token_form"
                       value="<?php echo attr(CsrfUtils::collectCsrfToken('autologin')); ?>"/>
                <input type="hidden" id="pid" name="id" value="<?php echo $id ?>">
                <input type="hidden" id="companyId" name="companyId" value="<?php echo $companyID ?>">
                <input type="hidden" name="id"
                       value="<?php echo $insuranceData['id'] ?? '' ?>"/>
                <div class="form-group">
                    <label for="patient_name" class="font-weight-bold"><?php echo xlt('Plan name'); ?></label>
                    <input type="text" class="form-control" id="plan_name"
                           name="plan_name" placeholder="Policy name" value="<?php echo $insuranceData['plan_name'] ?? '' ?>">
                </div>
                <div class="form-group">
                    <label for="DOB" class="font-weight-bold"><?php echo xlt('Policy number'); ?></label>
                    <input type="text" class="form-control" id="policy_number" value="<?php echo $insuranceData['policy_number'] ?? '' ?>"
                           name="policy_number" placeholder="Policy number" >
                </div>
                <div class="form-group">
                    <label for="phone_cell" class="font-weight-bold"><?php echo xlt('Group number'); ?></label>
                    <input type="text" class="form-control" id="group_number"
                           value="<?php echo $insuranceData['group_number'] ?? '' ?>"
                           name="group_number" placeholder="Group number">
                </div>
                <div class="row">
                    <div class="form-group col-md-12 col-lg-6 col-sm-12">
                        <label for="state" class="font-weight-bold"><?php echo xlt('First name'); ?></label>
                        <input type="text" class="form-control" id="f_name"
                               value="<?php echo $insuranceData['subscriber_fname'] ?? '' ?>" name="f_name" placeholder="First name">
                    </div>
                    <div class="form-group col-md-12 col-lg-6 col-sm-12">
                        <label for="postal_code" class="font-weight-bold"><?php echo xlt('Middle name'); ?></label>
                        <input type="text" class="form-control" id="m_name"
                               value="<?php echo $insuranceData['subscriber_mname'] ?? '' ?>" name="m_name"
                               placeholder="Last name">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12 col-lg-6 col-sm-12">
                        <label for="street" class="font-weight-bold"><?php echo xlt('Last name'); ?></label>
                        <input type="text" class="form-control" id="l_name"
                               value="<?php echo $insuranceData['subscriber_lname'] ?? '' ?>" name="l_name" placeholder="Last name">
                    </div>
                    <div class="form-group col-md-12 col-lg-6 col-sm-12">
                        <label for="city" class="font-weight-bold"><?php echo xlt('DOB'); ?></label>
                        <input type="text" class="form-control" id="DOB"
                               value="<?php echo isset($insuranceData['subscriber_DOB']) ? date('m/d/Y', strtotime($insuranceData['subscriber_DOB'])) : '' ?>"
                               name="DOB" placeholder="mm/dd/yyyy">

                    </div>
                </div>

                <div class="form-group text-center">
                    <input type="submit" class="btn btn-primary mt-2 w-50" id="submitButton" value="<?php echo xlt('Update'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    let errorMessage = $("#error-message").text();
    const hasError = errorMessage.length > 0;
    $("#submitButton").prop('disabled', hasError).prop('readonly', hasError);
    if(errorMessage !== 'No insurance data found'){
        $('input').on('input', function() {
            $("#submitButton").prop('disabled', false).prop('readonly', false);
        });
    }
</script>
</body>
</html>
