<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024. Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 */

namespace Juggernaut\SimpleStorageSolution\Module;

use Aws\S3\S3Client;
use OpenEMR\Common\Crypto\CryptoGen;

class Credentials
{
    private S3Model $s3Model;
    private array|bool $credentials;

    public final function __construct(S3Model $s3Model)
    {
        $this->s3Model = $s3Model;
        $this->credentials = $this->s3Model->getAllCredentials();
    }

    public function authenticateCredentials(): S3Client
    {
        if (!$this->checkCredentials()) {
            return new S3Client(['region' => $this->credentials['s3_region']]);
        } else {
            $token = new S3Token();
            $tokenResults = $token->getToken();
            return new S3Client([
                'region' => $this->credentials['s3_region'],
                'credentials' => [
                    'key' => $tokenResults['Credentials']['AccessKeyId'],
                    'secret' => $tokenResults['Credentials']['SecretAccessKey'],
                    'token' => $tokenResults['Credentials']['SessionToken']
                ]
            ]);
        }
    }

    private function checkCredentials(): bool
    {
        $cryptoGen = new CryptoGen();
        $key = $cryptoGen->decryptStandard($this->credentials['s3_key']);
        $secret = $cryptoGen->decryptStandard($this->credentials['s3_secret']);
        if (empty($key) || empty($secret)) {
            return false;
        }
        return true;
    }
}
