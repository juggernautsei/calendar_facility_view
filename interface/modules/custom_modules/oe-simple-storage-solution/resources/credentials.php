<?php

/**
 * package   OpenEMR
 *  link      http//www.open-emr.org
 *  author    Sherwin Gaddis <
 * license   https//github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 * All rights reserved
 **/

use Juggernaut\SimpleStorageSolution\Module\S3Model;
use OpenEMR\Common\Crypto\CryptoGen;

require_once __DIR__ . '/../vendor/autoload.php';
require_once dirname(__DIR__, 4) . '/globals.php';

$cryptoGen = new CryptoGen();

$s3Data = new S3Model();

    $s3Data->id = (int)$_POST['id'];
    $s3Data->s3_key = $cryptoGen->encryptStandard($_POST['s3_key']);
    $s3Data->s3_secret = $cryptoGen->encryptStandard($_POST['s3_secret']);
    $s3Data->s3_region = $_POST['s3_region'];
    $s3Data->s3_bucket = $_POST['s3_bucket'];
    $s3Data->persist();

    header('Location: ../settings.php');
//for right now we are going to assume only one set of credentials
