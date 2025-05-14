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

class DexcomAuth
{
    public static string $redirect;
    public function __construct()
    {
        self::$redirect = Bootstrap::DEXCOM_REDIRECT .
            Bootstrap::MODULE_INSTALLATION_PATH .
            Bootstrap::MODULE_NAME . '/' .
            Bootstrap::DEXCOM_REDIRECT_FOLDER . '/' .
            Bootstrap::DEXCOM_REDIRECT_FILE;
    }

    public static function authBuildReturnUrl(): string
    {
        return Bootstrap::DEXCOM_AUTH_URI . '/v2/oauth2/login?client_id=' . Bootstrap::DEXCOM_CLIENT_ID .'&redirect_uri='
            . self::$redirect . '&response_type=code&scope=offline_access&state=';
    }
}
