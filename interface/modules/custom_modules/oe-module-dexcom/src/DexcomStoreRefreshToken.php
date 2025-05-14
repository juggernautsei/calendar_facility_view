<?php
/*
 *   package   OpenEMR
 *   link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c)
 *  All rights reserved
 *
 */

namespace Juggernaut\Dexcom\Module;

class DexcomStoreRefreshToken
{
    private mixed $refreshToken;
    private mixed $pid;

    public function __construct($refreshToken)
    {

        if (isset($_COOKIE['dexcom'])) {
            $content = $_COOKIE['dexcom'];
            $this->pid = (int)$content;
        } else {
            $this->pid = $_SESSION['pid'];
        }

        $this->refreshToken = $refreshToken;
        $exists = $this->doesRefreshTokenExist();
        if (!$exists) {
            $this->storeRefreshToken();
        } else {
            $this->updateRefreshToken();
        }
    }
    private function storeRefreshToken(): bool
    {
        if (empty($this->refreshToken) || empty($this->pid)) {
            return false;
        }
        //There should be only one refresh token per patient
        sqlStatement("INSERT INTO `module_dexcom_credentials` (`pid`, `refresh_token`, `date_created`, `date_modified`)  VALUES (?, ?, NOW(), null)",
            [$this->pid, $this->refreshToken]);
        return true;
    }
    private function doesRefreshTokenExist(): bool|array|null
    {
        return sqlQuery("SELECT `pid` FROM `module_dexcom_credentials` WHERE `pid` = ?", [$this->pid]);
    }

    private function updateRefreshToken(): void
    {
        sqlStatement("UPDATE `module_dexcom_credentials` SET `refresh_token` = ?, `date_modified` = NOW() WHERE `pid` = ?",
            [$this->refreshToken, $this->pid]);
    }
}
