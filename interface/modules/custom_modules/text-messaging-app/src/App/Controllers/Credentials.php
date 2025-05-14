<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

namespace Juggernaut\Text\Module\App\Controllers;

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Common\Crypto\CryptoGen;

class Credentials
{
    /**
     * @return string
     */
    public function saveCredentials(): void
    {
        if (!CsrfUtils::verifyCsrfToken($_POST["token"])) {
            CsrfUtils::csrfNotVerified();
        }
        $status = 'success';
        $settingsType = 'credentials';
        $crypto = new CryptoGen();
        $accountSid = $crypto->encryptStandard($_POST['accountSid']);
        $smsApiKey = $crypto->encryptStandard($_POST['smsApiKey']);
        //sanitize the twilio number as text
        $smsnumber = filter_var($_POST['smsnumber'], FILTER_SANITIZE_SPECIAL_CHARS);
        $messagingServiceSid = filter_var($_POST['messagingServiceSid'], FILTER_SANITIZE_SPECIAL_CHARS);
        //we are going to store the credentials in the database as an encrypted json string
        $data = [
            'accountSid' => $accountSid,
            'smsApiKey' => $smsApiKey,
            'twilioNumber' => $smsnumber,
            'messagingServiceSid' => $messagingServiceSid
        ];
        $storedData = json_encode($data);
        try {
            $this->storeCredentials($settingsType, $storedData);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $status = 'error';
        }
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '/interface/modules/custom_modules/text-messaging-app/welcome.php?status=' . $status);
    }

    private function storeCredentials($settingsType, $storedData)
    {
        $sql = "INSERT INTO text_settings (`setting_id`, `settings_type`, `settings`) VALUES (NULL, ?, ?)";
        return sqlStatement($sql, [$settingsType, $storedData]);
    }

    public function getCredentials()
    {
        $sql = "SELECT * FROM text_settings WHERE settings_type = 'credentials' ORDER BY setting_id DESC LIMIT 1";
        return sqlQuery($sql);
    }

    public function decryptedCredentials()
    {
        $data = $this->getCredentials();
        $crypto = new CryptoGen();
        $decryptedData = json_decode($data['settings'], true);
        $decryptedData['accountSid'] = $crypto->decryptStandard($decryptedData['accountSid']);
        $decryptedData['smsApiKey'] = $crypto->decryptStandard($decryptedData['smsApiKey']);
        return $decryptedData;
    }
}
