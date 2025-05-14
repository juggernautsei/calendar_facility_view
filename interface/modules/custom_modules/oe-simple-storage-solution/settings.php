<?php

/*
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 *
 */


require_once dirname(__FILE__, 4) . "/globals.php";

use Juggernaut\SimpleStorageSolution\Module\InstanceProfileProvider;
use Juggernaut\SimpleStorageSolution\Module\S3Token;
use OpenEMR\Common\Crypto\CryptoGen;
use OpenEMR\Core\Header;
use OpenEMR\Common\Csrf\CsrfUtils;
use Juggernaut\SimpleStorageSolution\Module\S3Model;

$cryptoGen = new CryptoGen();
$s3Data = new S3Model();
$awsData = $s3Data->getAllCredentials();
$getBuckets = new InstanceProfileProvider();
$bucketList = $getBuckets->getTheBuckets();
$region_msg = 'Must enter AWS Region first';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo attr("AWS Simple Storage Solution"); ?></title>
    <?php echo Header::setupHeader() ?>
</head>
<body>
<div class="container mt-5">
    <h2 class="m-5"><?php echo xlt("AWS Simple Storage Solution"); ?></h2>
    <div class="row">
        <div class="col-5 mr-1">
            <h3><?php echo xlt('Settings') ?></h3>
            <p class="text-danger small"><?php echo "*" . xlt('Required to get a token if using key and secret')  ?></p>
            <form method="post" action="resources/credentials.php">
                <input type="hidden" name="csrf_token" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>">
                <input type="hidden" id="id" name="id" value="<?php echo $awsData['id']; ?>">
                <?php if (!empty($awsData['id'])) {
                    $region_msg = 'Use only to Change AWS Region';
                    ?>
                <input type="text" class="form-control m-2" name="s3_key" placeholder="<?php echo attr('AWS Static Key') ?> *" value="">
                <input type="text" class="form-control  m-2"  name="s3_secret" placeholder="<?php echo attr('AWS Secret') ?> *" value="">
                <?php } ?>
                <input type="text" class="form-control  m-2"  name="s3_region" placeholder="<?php echo attr($region_msg) ?> " value="">
                <?php if (!empty($awsData['id'])): ?>
                <p class="text small"><?php echo xlt('If keys are saved, they will not be displayed here for security reasons') ?>.</p>
                <?php endif; ?>
                <input type="submit"  class="btn btn-primary"  name="submit" value="Save">
            </form>
        </div>
        <div class="col-5 ml-1">
            <?php if (empty($awsData['id'])): ?>
                <h4><?php echo xlt('Step 1') ?></h4>
                <p><?php echo xlt('Enter region'); ?></p>
            <?php endif; ?>
            <?php if (!empty($awsData['id'])): ?>
                <h4><a href="https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html" target="_blank"><?php echo xlt('AWS Credential Settings Guide') ?></a></h4>
                <p><?php echo "<strong>" . xlt('Use Instance Profile or Key & Secret. ') . "<br>" . xlt('DO NOT USE BOTH ONLY ONE IS NEEDED') . "</strong>"?></p>
                <p><?php echo xlt('1. If there is no list of buckets below. That means an Instance Profile is not set.'); ?></p>
                <p><?php echo xlt('2. If you are going to use an Instance Profile for this server, configure and enable it now'); ?></p>
                <p><?php echo xlt('3. If you are going to use a static key, enter the key and secret to the left.'); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $key = $cryptoGen->decryptStandard($awsData['s3_key']);
    if (!empty($awsData['s3_region'])) {
        $region = $awsData['s3_region'];
        ?>
    <div class="row mt-5">
        <div class="col-4 mr-2">
            <h3><?php echo xlt('Region Setting') ?></h3>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col"><?php echo xlt('Region') ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?php echo $region; ?></td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php } ?>
        <?php if (!empty($key)) { ?>
            <div class="col-6 ml-5">
                <h3><?php echo xlt('Key Access Verification') ?></h3>
                <?php
                $token = new S3Token();
                $tokenData = $token->getToken();
                if (!empty($tokenData['Credentials']['SessionToken'])) {
                    echo "<span class='text-success'><strong>" . xlt('Session Token Verified') . "</strong></span>";
                } else {
                    echo "<span class='text-danger'>" .  xlt('Token Failed') . "</span><br>";
                    echo xlt('Please check your region and version');
                }
                $bucketData = $token->listBuckets();
                echo "<h3 class='mt-2'>" . xlt('Available Buckets') . "</h3>";
                foreach ($bucketData['Buckets'] as $bucket) {
                    echo "<span class='text-success'><strong>" . xlt($bucket['Name']) . "</strong></span><br>";
                }
                if (empty($bucketData['Buckets'])) {
                    echo "<br><span class='text-danger'>" . xlt('Something in your credentials is wrong or you have not created any buckets yet.') . "</span>";
                }
                ?>
            </div>
        <?php } ?>
        <?php
        if (!empty($bucketList['Buckets'])) {
            ?>
            <div class="col-6 ml-5">
                <h3><?php echo xlt('Instance Access Verification') ?></h3>
                <?php
                echo "<h4 class='mt-2'>" . xlt('Available Buckets') . "</h4>";
                echo "<p>" . xlt('Select a bucket to store in the database') . "</p>";
                foreach ($bucketList['Buckets'] as $bucket) {
                    echo "<input type='radio' id='bucket' value = '" . attr($bucket['Name']) . "'";
                    if ($awsData['s3_bucket'] == $bucket['Name']) {
                        $selected = 'checked';
                    }
                    echo  " " . $selected;
                    echo "> <span class='text-success'><strong>" . xlt($bucket['Name']) . "</strong></span><br>";
                }
                ?>
                <a class="mt-3 btn-primary btn" href="../oe-simple-storage-solution/public/index.php"><?php echo xlt('Test upload to the selected bucket') ?></a>
            </div>
        <?php } ?>
    </div>
</div>
</body>
<script>
    //add event listener to select bucket
    document.getElementById('bucket').addEventListener('change', selectBucket);
    function selectBucket() {
        const id = document.getElementById('id').value
        const bucket = document.getElementById('bucket').value;
        const region = '<?php echo $region; ?>';
        console.log(bucket);
        //write an ajax call to resources/credentials.php to post the data of the selected bucket
        $.ajax({
            type: 'POST',
            url: 'resources/credentials.php',
            data: {id: id, s3_bucket: bucket, s3_region: region},
            success: function (data) {
                console.log(data);
            }
        });
    }
</script>
</html>
