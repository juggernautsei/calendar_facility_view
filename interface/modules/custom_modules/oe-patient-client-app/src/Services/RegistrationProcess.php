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
use OpenEMR\Common\Crypto\CryptoGen;

class RegistrationProcess
{
    private mixed $registrationData;
    private $jseKey;
    private $regBusinessData;
    private CryptoGen $cryptoGen;

    public function __construct($data = [])
    {
        $this->registrationData = $data;
        $this->cryptoGen = new CryptoGen();
    }
    public static function checkRegistrationStatus(): bool
    {
        $registrationStatus = false;
        if (isset($_POST['register'])) {
            $email = $_POST['email'];
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $registrationStatus = true;
            }
        }
        return $registrationStatus;
    }

    public function getApiKey()
    {
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json'
        ];
        $body = $this->registrationData;
        $request = new Request('POST',
            'https://app.myhealthdataconnect.com/api/client/signup', $headers, $body);
        $response = $client->sendAsync($request)->wait();

        $this->jseKey = $response->getBody();
        $this->storeApiKeyInfo();
        //store the returned api key and registration data in the database and return a completion status
        return $this->jseKey;
    }

    private function storeApiKeyInfo()
    {
        $regData = json_decode($this->registrationData, true);
        $keyData = json_decode($this->jseKey, true);
        $saveData = new StoreAppApiKey();
        $saveData->fullname = $regData['name'];
        $saveData->email = $regData['email'];
        $saveData->remote_password = $this->cryptoGen->encryptStandard($regData['password']); ;
        $saveData->remote_id = $keyData['remote_id'];
        $saveData->api_key = $this->cryptoGen->encryptStandard($keyData['api_key']);
        return $saveData->storeApiKey();
    }

    public function pushBusinessData()
    {
        $x_api_key = json_decode($this->registrationData, true);
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'x-api-key' => $x_api_key['apikey'], //$this->cryptoGen->decryptStandard($this->registrationData['apikey']),
        ];
        $body = $this->registrationData;
        $request = new Request('POST',
            'https://app.myhealthdataconnect.com/api/client', $headers, $body);
        $response = $client->sendAsync($request)->wait();

        $this->regBusinessData = $response->getBody();

        $status = json_decode($this->regBusinessData, true);
        $remoteId = json_decode($body, true);
        //store the entry id that is returned from the api call
        $storeForeignData = new StoreAppApiKey();
        $storeForeignData->updateAccountData(
            (int) $status['id'],
            $remoteId['remote_id']
        );

        //store the returned api key and registration data in the database and return a completion status
        return $this->regBusinessData;
    }
}
