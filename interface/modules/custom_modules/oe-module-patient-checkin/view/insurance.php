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

$insurance = new InsuranceCheck();
$id = '';
$insuranceData = [];
if(isset($_GET['id'])) {
        $id = $_GET['id'];
        $insuranceData = $insurance->checkInsuranceDetails($id);
}
$isId = $insuranceData['provider'] ?? 'Panther';

    $providerName = $insurance->getInsuranceCompanies($isId);
//we can use sessions and hide the PID
CsrfUtils::setupCsrfKey();

?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Patient Checkin'); ?></title>
    <?php Header::setupHeader(['common']);?>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-12">
            <?php if(empty($insuranceData['message'])) { ?>
            <h3 class="text-center mt-5"><?php echo xlt('Do you have a new insurance card?'); ?></h3>
            <?php } else { ?>
                <h3 class="text-center mt-5"><?php echo xlt('No insurance data found'); ?></h3>
            <?php } ?>
        </div>
    </div>
    <div class="row">
        <?php if(empty($insuranceData['message'])) { ?>
        <div class="col-md-12 col-lg-5 col-sm-12 mx-auto mt-2">
            <div class="form-group">
                <label for="patient_name" class="font-weight-bold"><?php echo xlt('Provider Name'); ?> : </label>
                <span><?php echo $providerName; ?></span>
            </div>
            <div class="form-group">
                <label for="patient_name" class="font-weight-bold"><?php echo xlt('Policy number'); ?> : </label>
                <span><?php echo $insuranceData['policy_number'] ?? ''; ?></span>
            </div>
            <?php if (!empty($insuranceData['plan_name'])): ?>
            <div class="form-group">
                <label for="patient_name" class="font-weight-bold"><?php echo xlt('Plan name'); ?> : </label>
                <span><?php echo $insuranceData['plan_name'] ?? ''; ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($insuranceData['group_number'])): ?>
            <div class="form-group">
                <label for="patient_name" class="font-weight-bold"><?php echo xlt('Group number'); ?> : </label>
                <span><?php echo $insuranceData['group_number'] ?? ''; ?></span>
            </div>
            <?php endif; ?>
            <div class="form-group text-center">
                <a href="../view/payment.php?id=<?php echo $id; ?>">
                    <button type="button" class="btn btn-primary w-100 mb-3"><?php echo xlt('No Continue'); ?></button>
                </a><br>
                <a href="../view/insurance_change.php?id=<?php echo $id; ?>">
                    <button type="button" class="btn btn-primary w-100 mb-3"><?php echo xlt('Yes Update'); ?></button>
                </a><br>
                <a href="../view/patient_change_data.php?id=<?php echo $id; ?>">
                    <button type="button" class="btn btn-primary w-100"><?php echo xlt('Back'); ?></button>
                </a>
            </div>
        </div>
        <?php } else { ?>
            <div class="col-md-12 col-lg-5 col-sm-12 mx-auto mt-2">
                <div class="form-group text-center">
                    <a href="../view/payment.php?id=<?php echo $id ?>"><button type="button"
                                class="btn btn-primary w-50"><?php echo xlt('Next'); ?></button></a>
                </div>
                <div class="form-group text-center">
                    <a href="../public/index.php?id=<?php echo $id; ?>" onclick="top.restoreSession()"><button class="btn btn-primary w-50"><?php echo xlt('Back'); ?></button></a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
</body>
</html>
