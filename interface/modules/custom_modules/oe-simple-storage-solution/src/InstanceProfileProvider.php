<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024. Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 */

namespace Juggernaut\SimpleStorageSolution\Module;

use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;

class InstanceProfileProvider
{
    public final function getTheBuckets()
    {
        $region = new S3Model();
        $theRegion = $region->getAllCredentials();
        if (empty($theRegion['s3_region'])) {
            return false;
        }
        try {
            //The purpose of this class is for testing credentials during setup
            $provider = CredentialProvider::instanceProfile();
            // Be sure to memoize the credentials
            $memoizedProvider = CredentialProvider::memoize($provider);

            $client = new S3Client([
                'region' => $theRegion['s3_region'],
                'version' => 'latest',
                'credentials' => $memoizedProvider
            ]);

            return $client->listBuckets();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $e->getMessage();
        }
    }
}
