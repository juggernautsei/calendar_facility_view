<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024. Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 */

namespace Juggernaut\SimpleStorageSolution\Module;

use OpenEMR\Common\ORDataObject\ORDataObject;

class S3Model extends ORDataObject
{
    public int $id;
    public mixed $s3_key;
    public mixed $s3_secret;
    public string $s3_region;

    public function __construct($id = "", $_prefix = "")
    {
        parent::__construct();
        $this->_table = "module_s3_credentials";
        if (is_numeric($id)) {
            $this->id = $id;
        } else {
            $id = "";
        }
    }

    public function get_id(): int
    {
        return $this->id;
    }

    public function set_id(int $id): void
    {
        $this->id = $id;
    }

    public function set_s3_bucket(mixed $s3_bucket): void
    {
        $this->s3_bucket = $s3_bucket;
    }

    public function get_s3_bucket(): mixed
    {
        return $this->s3_bucket;
    }
    public function get_s3_key(): mixed
    {
        return $this->s3_key;
    }

    public function set_s3_key(mixed $s3_key): void
    {
        $this->s3_key = $s3_key;
    }

    public function get_s3_secret(): mixed
    {
        return $this->s3_secret;
    }

    public function set_s3_secret(mixed $s3_secret): void
    {
        $this->s3_secret = $s3_secret;
    }

    public function get_s3_region(): string
    {
        return $this->s3_region;
    }

    public function set_s3_region(string $s3_region): void
    {
        $this->s3_region = $s3_region;
    }
    public function getAllCredentials(): array|bool
    {
        $sql = "SELECT * FROM module_s3_credentials";
        return sqlQuery($sql);
    }
}
