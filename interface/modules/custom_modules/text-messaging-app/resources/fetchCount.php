<?php

require_once dirname(__DIR__, 4) . '/globals.php';

use OpenEMR\Common\Crypto\CryptoGen;
function getTextBalanceCount()
{
    $key = new CryptoGen();
    $textKey = $key->decryptStandard($GLOBALS['texting_enables']);
    if (empty($textKey)) {
        return;
    }
    $url = "https://textbelt.com/quota/$textKey";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
$rawCount = getTextBalanceCount();

$count = json_decode($rawCount, true);

echo json_encode(['count' => $count['quotaRemaining']]);
