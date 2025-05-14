<?php

Header("X-Frame-Options: DENY");
Header("Content-Security-Policy: frame-ancestors 'none'");

$ignoreAuth = true;
$sessionAllowWrite = true;
require_once dirname(__DIR__, 4)."/globals.php";
require_once '../src/Services/InsuranceCheck.php';
require_once '../src/Services/PatientCheck.php';

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
use Juggernaut\Module\PatientCheckin\InsuranceCheck;
use Juggernaut\Module\PatientCheckin\PatientCheck;
use OpenEMR\Common\Auth\OneTimeAuth;

$pid = $_GET['id'] ?? '';

if (empty($pid)) {
    header("Location: ../public/index.php");
    exit();
}
//gather information for the patient portal
/*------------------------------------------------------------*/
$patient = new PatientCheck();
$patientEmail = $patient->getPatientEmail($pid);
$domain = $patient->buildURL();
/*____________________________________________________________*/

$insurance = new InsuranceCheck();
$balanceData = 0;

$balanceData = $insurance->checkBalanceDetails($pid);


CsrfUtils::setupCsrfKey();

// Generate one-time authentication token for the patient portal
$auth = new OneTimeAuth();
$tokenParams = [
    'pid' => $pid,
    'redirect_link' => $domain,
    'email' => $patientEmail['email_direct'],
    'expiry_interval' => 'PT15M' // Token expires in 15 minutes
];

try {
    $tokenResponse = $auth->createPortalOneTime($tokenParams);
} catch (Exception $e) {
    $tokenResponse = ['encoded_link' => ''];
    error_log($e->getMessage());
}

?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title><?php echo xlt('Payment Page'); ?></title>
    <?php Header::setupHeader(); ?>
</head>
<body>
<div class="container text-center align-middle mt-5">
    <div class="row mt-4">
        <div class="col-12 mt-5">
            <h2><?php echo xlt('Patient Payment'); ?></h2><br>
            <p><?php echo xlt('Your outstanding balance is: '); ?>$<?php echo number_format($balanceData, 2); ?></p>
            <br>
            <?php
            if ($balanceData > 0 && isset($tokenResponse['encoded_link'])):
                if (empty($tokenResponse['encoded_link'])) {
                    echo '<p class="text-danger">' . xlt('Error: Portal Credentials not set, inform front desk') . '</p>';
                } else {
                ?>
                <p>
                    <a href="<?php echo htmlspecialchars($tokenResponse['encoded_link']); ?>" class="btn btn-primary w-25">
                        <?php echo xlt('Make Payment'); ?>
                    </a>
                </p>
                    <p><a href="checkin_complete.php" class="btn btn-primary w-25"><?php echo xlt('Skip'); ?></a></p>
            <?php  } ?>
            <?php else: ?>
                <p><a href="checkin_complete.php" class="btn btn-primary w-25"><?php echo xlt('Next'); ?></a></p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
