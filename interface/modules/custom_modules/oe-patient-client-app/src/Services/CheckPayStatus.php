<?php

/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

namespace Juggernaut\Module\App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class CheckPayStatus
{
    public function checkPaymentStatus($clientId, $key): array|bool|null
    {

        $client = new Client();
        $headers = [
            'x-api-key' => $key
        ];
        $request = new Request('GET', "https://app.myhealthdataconnect.com/api/paid/$clientId", $headers);
        $res = $client->sendAsync($request)->wait();
        $status = $res->getBody();
        return json_decode($status, true);
    }
}
