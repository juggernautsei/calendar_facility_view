<?php

/**
 *
 *  package   OpenEMR
 *  link      https://affordablecustomehr.como
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 *
 */

require_once dirname(__FILE__, 5) . "/globals.php";

use OpenEMR\Core\Header;
use Juggernaut\SimpleStorageSolution\Module\PutObjectInBucket;
use Juggernaut\SimpleStorageSolution\Module\S3Model;

$s3Model = new S3Model();

$putObjectInBucket = new PutObjectInBucket($s3Model);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt("Documentation Reminders"); ?></title>
    <?php Header::setupHeader(['common'])?>
</head>
<body>
    <div class="container-lg">
        <div class="mt-5">
            <h2><?php echo xlt("AWS Simple Storage Solution upload"); ?></h2>
        <?php
            $fileNameAndUnigid = dirname(__FILE__) . '/text-' . uniqid() . '.txt';
            $sourceFile = dirname(__FILE__) . '/text.txt';
            $results = $putObjectInBucket->putObjectInBucket($fileNameAndUnigid, $sourceFile);
            if ($results) {
                echo "<span class='text-success mt-3 mb-5'><strong>File uploaded successfully. Check your bucket for text file</strong></span>";
            } else {
                echo "File failed to upload";
            }
        ?>
		</div>
		<div class="">
		</div>
		&copy; <?php echo date('Y') . " Juggernaut Systems Express" ?>
    </div>
</body>
</html>
