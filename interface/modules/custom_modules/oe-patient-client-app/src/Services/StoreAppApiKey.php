<?php
/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

namespace Juggernaut\Module\App\Services;

use OpenEMR\Common\ORDataObject\ORDataObject;

class StoreAppApiKey extends ORDataObject
{
    public const TABLE_NAME = 'module_patient_app';
    public $fullname;
    public $email;
    public $remote_password;
    public $remote_id;
    public $api_key;

    public function storeApiKey()
    {
        $sql = "INSERT INTO " . self::TABLE_NAME . " (fullname, email, remote_password, remote_id, api_key) VALUES (?, ?, ?, ?, ?)";
        return sqlStatement($sql, [$this->fullname, $this->email, $this->remote_password, $this->remote_id, $this->api_key]);
    }

    public function getApiKey()
    {
        $sql = "SELECT api_key, remote_id, identifier_data FROM " . self::TABLE_NAME;
        return sqlQuery($sql);
    }

    public function updateAccountData($iid, $remote_id)
    {
        $sql = "UPDATE " . self::TABLE_NAME . " SET indentifier_data = ? WHERE remote_id = ?";
        sqlStatement($sql, [$iid, $remote_id]);
    }
}
