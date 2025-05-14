<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024. Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 */

namespace Juggernaut\SimpleStorageSolution\Module;

use Aws\Result;
use Aws\S3\S3Client;
use OpenEMR\Common\Crypto\CryptoGen;
use Aws\Credentials\Credentials;
use Aws\Sts\StsClient;
class S3Token
{
    private Credentials $credentials;
    private mixed $region;
    private mixed $bucket;
    private mixed $endpoint;
    private mixed $version;
    private $stsClient;

    public final function __construct()
    {
        $keyAndSecrete = new S3Model();
        $credentialData = $keyAndSecrete->getAllCredentials();
        $this->region = $credentialData['s3_region'];
        $this->bucket = $credentialData['s3_bucket'];
        $this->endpoint = $credentialData['s3_endpoint'];
        $this->version = $credentialData['s3_version'];
        $cryptoGen = new CryptoGen();
        $key = $cryptoGen->decryptStandard($credentialData['s3_key']);
        $secret = $cryptoGen->decryptStandard($credentialData['s3_secret']);
        $this->credentials = new Credentials($key, $secret);
    }
    public final function getToken(): Result
    {
        $stsClient = new StsClient([
            'region' => $this->region,
            'credentials' => $this->credentials
        ]);
        return $stsClient->getSessionToken();
    }
    public final function listBuckets(): Result
    {
        $result = $this->getToken();
        $s3Client = new S3Client([
            'region' => $this->region,
            'credentials' => [
                'key' => $result['Credentials']['AccessKeyId'],
                'secret' => $result['Credentials']['SecretAccessKey'],
                'token' => $result['Credentials']['SessionToken']
            ]
        ]);
        return $s3Client->listBuckets();
    }
}
