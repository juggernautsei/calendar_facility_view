<?php

/*
 * @package openemr
 * @link https://www.open-emr.org
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright (c) 2023
 * @license All Rights Reserved
 */

namespace OpenEMR\Modules\Clover\Controller;

use Guzzle\GuzzleHttp\GuzzleException;
use Guzzle\Http\Client;

class CloverPayCharge
{
    public $jsonBody;
    const URL = 'https://scl.clover.com';
    public string $ipaddress;

    public function __construct()
    {
        $checkSite = new IsSiteReachable('/v1/charges');
        $checkSite->isSiteReachable();
    }

    public function processPatientCharge()
    {
        $client = new \GuzzleHttp\Client();
        $oauth2 = new CloverConnectController();
        $oauth2_token = $oauth2->getAppCredentials();
        try {
            $response = $client->request('POST', $this::URL . '/v1/charges', [
                'body' => $this->jsonBody,
                'headers' => [
                    'accept' => 'application/json',
                    'authorization' => 'Bearer ' . $oauth2_token['app_secret'],
                    'content-type' => 'application/json',
                    'x-forwarded-for' => $this->ipaddress,
                ],
            ]);
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
        return $response->getBody();
    }
}
