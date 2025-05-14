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
class CreateCardToken
{
    private $apiAccessKey;
    /**
     * @var mixed|null
     */
    private mixed $bodyJson;

    const URL = 'https://token.clover.com';

    public function __construct($apiAccessKey = null, $bodyJson = null)
    {
        $this->apiAccessKey = $apiAccessKey;
        $this->bodyJson = $bodyJson;
    }

    public function tokenizeCard()
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', $this::URL . '/v1/tokens', [
            'body' => $this->bodyJson,
            'headers' => [
                'accept' => 'application/json',
                'apikey' => $this->apiAccessKey,
                'content-type' => 'application/json',
            ],
        ]);

        return $response->getBody();
    }
}
