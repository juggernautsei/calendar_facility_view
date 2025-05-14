<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024. Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 */

namespace Juggernaut\SimpleStorageSolution\Module;

use Exception;

class PutObjectInBucket
{
    private S3Model $s3Model;
    private array $region;

    public final function __construct(S3Model $s3Model)
    {
        $this->s3Model = $s3Model;
        $this->region = $this->s3Model->getAllCredentials();
    }

    public final function putObjectInBucket($fileNameAndUnigid, $FileSource): bool
    {
        $authCredentials = new Credentials($this->s3Model);
        $s3client = $authCredentials->authenticateCredentials();
        try {
            $s3client->putObject([
                'Bucket' => $this->region['s3_bucket'],
                'Key' => $fileNameAndUnigid,
                'SourceFile' => $FileSource
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Failed to upload $fileNameAndUnigid with error: " . $e->getMessage() . "\n");
            return false;
        }
    }
}
