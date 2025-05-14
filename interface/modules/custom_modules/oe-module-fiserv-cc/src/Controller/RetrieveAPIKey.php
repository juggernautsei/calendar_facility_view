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

class RetrieveAPIKey
{
    const URL = 'https://apisandbox.dev.clover.com';

    public function cloverApiKey()
    {
        try {
            $access_token = new CloverConnectController();
            $token = $access_token->getAppCredentials();
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $this::URL . '/pakms/apikey', [
                'headers' => [
                    'accept' => 'application/json',
                    'authorization' => "Bearer " . $token['app_secret'],
                ],
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->hasResponse()) {
                if ($e->getResponse()->getStatusCode() == 401) {
                    return $e->getResponse()->getBody()->getContents();
                }
            }
        }
        return $response->getBody();
    }

    public function checkAccessToken()
    {
        $sql = "SELECT `app_secret` FROM `module_clover_credentials`";
        $result = sqlQuery($sql);
        if (empty($result['app_secret'])) {
            return false;
        }
        return true;
    }
}
