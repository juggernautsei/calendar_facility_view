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

class DexconDataRequest
{
    public array $accessToken;
    public function patientReading(): bool|string
    {
        /**
         * Requires libcurl
         */
        $yesterday = date("Y-m-d", strtotime("-1 day")); //get yesterday

        /*$query = array(
            "startDate" => $yesterday . "T00:00:00",
            "endDate" => $yesterday . "T11:59:59"
        );*/
        $query = array(
            "startDate" => "2023-06-21T00:00:00",
            "endDate" => "2023-06-21T11:59:59"
        );
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $this->accessToken['access_token'],
            ],
            CURLOPT_URL => Bootstrap::DEXCOM_AUTH_URI . "/v2/users/self/egvs?" . http_build_query($query),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
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
