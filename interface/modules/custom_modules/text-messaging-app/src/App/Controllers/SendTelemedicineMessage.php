<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * All rights reserved
 */

namespace Juggernaut\Text\Module\App\Controllers;

use OpenEMR\Common\Crypto\CryptoGen;

class SendTelemedicineMessage
{
    const TEXT_PROVIDER_URL = 'https://textbelt.com/text';
    public static function outBoundTelemedicineMessage(string $phone, string $message) : void
    {
        $key = self::getKey();
        $ch = curl_init(self::TEXT_PROVIDER_URL);
        $data = array(
            'phone' => $phone,
            'message' => $message,
            'key' => $key,
        );

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_exec($ch);
        curl_close($ch);
    }

    private static function getKey()
    {
        $key = new CryptoGen();
        return $key->decryptStandard($GLOBALS['texting_enables']);
    }
}
