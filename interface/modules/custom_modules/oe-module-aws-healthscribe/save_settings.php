<?php

/*
 *
 * @package OpenEMR
 * @link    https://www.open-emr.org
 * author Sherwin Gaddis <sherwingaddis@gmail.com>
 * All Rights Reserved
 * @copyright Copyright (c) 2024.
 */

require_once dirname(__DIR__, 3) . '/globals.php';
use OpenEMR\Common\Crypto\CryptoGen;

$crypto = new CryptoGen();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST)) {
    header('Location: settings.php?error=empty_post');
    exit;
}
$access_key = $crypto->encryptStandard($_POST['access_key']);
$secret_key = $crypto->encryptStandard($_POST['secret_key']);

$sql = "INSERT INTO `module_aws_healthscribe_settings` (`access_key`, `secret_key`, `region`) VALUES (?, ?, ?)";
$result = sqlStatement($sql, array($access_key, $secret_key, $_POST['region']));
header('Location: setup.php');
exit;
