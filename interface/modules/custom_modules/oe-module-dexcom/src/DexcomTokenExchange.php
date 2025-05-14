<?php
/*
 *   package   OpenEMR
 *   link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c)
 *  All rights reserved
 *
 */

namespace Juggernaut\Dexcom\Module;

class DexcomTokenExchange
{
    public function getNewToken(): bool|string
    {
        if (!$this->storedRefreshToken()) {
            return false;
        }
        new DexcomAuth();
        $curl = curl_init();

        $payload = "grant_type=refresh_token&refresh_token=" . $this->storedRefreshToken() .
            "&redirect_uri=" . DexcomAuth::$redirect .
            "&client_id=" .
            Bootstrap::DEXCOM_CLIENT_ID . "&client_secret=" . Bootstrap::DEXCOM_CLIENT_SECRET;

        curl_setopt_array (
            $curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/x-www-form-urlencoded"
            ],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_URL => Bootstrap::DEXCOM_AUTH_URI . "/v2/oauth2/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return "cURL Error #:" . $error;
        } else {
            return $response;
        }
    }

    private function storedRefreshToken()
    {
        $token = sqlQuery("SELECT `refresh_token` FROM `module_dexcom_credentials` WHERE `pid` = ?", [$_SESSION['pid']]);
        if (!empty($token['refresh_token'])) {
            return $token['refresh_token'];
        } else {
            return false;
        }
    }
}
