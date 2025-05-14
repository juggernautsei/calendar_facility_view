<?php

/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

require_once dirname(__FILE__, 5) . '/globals.php';
require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Juggernaut\SimpleStorageSolution\Module\Credentials;
use Juggernaut\SimpleStorageSolution\Module\S3Model;

$s3model = new S3Model();

$credentials = new Credentials($s3model);
$s3Client = $credentials->authenticateCredentials();
$getBucket = $s3model->getAllCredentials();

$cmd = $s3Client->getCommand('GetObject', [
    'Bucket' => $getBucket['s3_bucket'],
    'Key' => '/Connecting-you-with-HCPs-2023.pdf'
]);

$request = $s3Client->createPresignedRequest($cmd, '+20 minutes');

$presignedUrl = (string) $request->getUri();

echo "<a href=" . $presignedUrl . ">Connecting-you-with-HCPs-2023.pdf<a>";

