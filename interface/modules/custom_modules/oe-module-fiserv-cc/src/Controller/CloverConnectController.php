<?php

/*
 * @package openemr
 * @link https://www.open-emr.org
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright (c) 2023
 * @license All Rights Reserved
 */

namespace OpenEMR\Modules\Clover\Controller;
class CloverConnectController
{
    public function storeAppCredentials($credentials)
    {
        $hasEntries = $this->getAppCredentials();

        if (!$hasEntries) {
            $sql = "INSERT INTO `module_clover_credentials` (`app_secret`, `app_id`, `raid`, `hook`) VALUES (?, ?, ?, ?)";
        } else {
            $sql = "UPDATE `module_clover_credentials` SET
                                       `app_secret` = ?,
                                       `app_id` = ?,
                                       `raid` = ?,
                                       `hook` = ?
                                   WHERE `module_clover_credentials`.`id` = 1";
        }
        return sqlStatement($sql, $credentials);
    }

    public function getAppCredentials()
    {
        return sqlQuery("SELECT * FROM `module_clover_credentials`");
    }
}
