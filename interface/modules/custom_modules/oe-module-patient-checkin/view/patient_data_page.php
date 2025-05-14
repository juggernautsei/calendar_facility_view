<?php

Header("X-Frame-Options: DENY");
Header("Content-Security-Policy: frame-ancestors 'none'");

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;

require_once dirname(__DIR__, 5) . "/interface/globals.php";

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;


if(isset($_GET['data'])) {
    $serializedData = $_GET['data'];
    $patient = unserialize(urldecode($serializedData));
    $_SESSION['pid'] = $patient['id'];
} else {
    header("Location: ../public/index.php");
    exit();
}

CsrfUtils::setupCsrfKey();

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Patient Checkin'); ?></title>
    <?php Header::setupHeader(['common']);?>
</head>
<body>
<div class="container align-middle mt-5">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <h3 class="text-center mt-5"><?php echo xlt('Is your info correct?'); ?></h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-lg-5 col-sm-10 mx-auto mt-2">
                <div class="form-group">
                    <label for="patient_name" class="font-weight-bold"><?php echo xlt('Patient Name'); ?> : </label>
                    <span><?php echo $patient['fname']; ?> <?php echo $patient['lname']; ?></span>
                </div>
                <div class="form-group">
                    <label for="patient_name" class="font-weight-bold"><?php echo xlt('DOB'); ?> : </label>
                    <span><?php echo $patient['DOB']; ?></span>
                </div>
                <div class="form-group">
                    <label for="patient_name" class="font-weight-bold"><?php echo xlt('Address'); ?> : </label>
                    <span><?php echo !empty($patient['street']) ? $patient['street'] . ', ' : '';
                        echo !empty($patient['city']) ? $patient['city'] . ', ' : '';
                        echo !empty($patient['state']) ? $patient['state'] . ', ' : '';
                        echo !empty($patient['postal_code']) ? $patient['postal_code'] : ''; ?></span>
                </div>
                <div class="form-group">
                    <label for="patient_name" class="font-weight-bold"><?php echo xlt('Cell Phone'); ?> : </label>
                    <span><?php echo $patient['phone_cell']; ?></span>
                <div class="form-group text-center">
                    <a href="../view/insurance.php?id=<?php echo $patient['id']; ?>">
                        <button type="button" class="btn btn-primary w-50"><?php echo xlt('Correct'); ?></button></a>
                </div>
                <div class="form-group text-center">
                    <a href="../view/patient_change_data.php?id=<?php echo $patient['id']; ?>" onclick="top.restoreSession()">
                        <button type="button" class="btn btn-primary w-50"><?php echo xlt('Change'); ?></button></a>
                </div>
        </div>
    </div>
</div>
</body>
</html>
