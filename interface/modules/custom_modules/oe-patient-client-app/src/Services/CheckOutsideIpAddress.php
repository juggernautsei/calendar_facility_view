<?php

/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

namespace Juggernaut\Module\App\Services;

class CheckOutsideIpAddress
{
    public function getOutsideIpAddress(): bool|string
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://ipinfo.io/ip");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $publicIP = curl_exec($curl);
        curl_close($curl);
        return $publicIP;
    }

    public function checkHttpsConnection($ipAddress): bool
    {
        $url = "https://$ipAddress" . "/interface/smart/register-app.php"; // Construct URL with HTTPS
        $curl = curl_init();

        // Set cURL options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); // Bypass SSL verification (not recommended for production)
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // Verify the certificate's name against host
        curl_setopt($curl, CURLOPT_HEADER, true); // Include the headers
        curl_setopt($curl, CURLOPT_NOBODY, true); // Do not include the body
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the transfer as a string
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); // Timeout for connection
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); // Timeout for the whole request

        // Execute cURL session
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);

        if ($response === false) {
            echo "cURL Error Cannot use: " . curl_error($curl);
            return false;
        }

        // Close cURL session
        curl_close($curl);

        // Check HTTP code in response
        if ($info['http_code'] == 200) {
            return true; // HTTP 200 indicates the server responded correctly
        } else {
            return false;
        }
    }
}
