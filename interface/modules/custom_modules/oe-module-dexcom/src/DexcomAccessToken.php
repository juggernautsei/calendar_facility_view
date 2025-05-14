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

class DexcomAccessToken
{
    public static function requestAccessToken($code): bool|string
    {
        new DexcomAuth();
        /**
         * Requires libcurl
         */

        $curl = curl_init();

        $payload = "grant_type=authorization_code&code=" . $code . "&redirect_uri=" . DexcomAuth::$redirect . "&client_id=" .
            Bootstrap::DEXCOM_CLIENT_ID . "&client_secret=" . Bootstrap::DEXCOM_CLIENT_SECRET;

        curl_setopt_array($curl, [
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
}
