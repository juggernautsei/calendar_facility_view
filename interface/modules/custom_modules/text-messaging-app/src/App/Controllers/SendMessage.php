<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  All Rights Reserved
 */

namespace Juggernaut\Text\Module\App\Controllers;

use OpenEMR\Common\Crypto\CryptoGen;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;
class SendMessage
{


    /**
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public static function outBoundMessage(int $phone, string $message): string
    {
        $credentials = new Credentials();
        $data = $credentials->decryptedCredentials();

        // A Twilio number you own with SMS capabilities
        $twilio_number = $data['smsnumber'];
        $messagingServiceSid = $data['messagingServiceSid'];
        $practice = self::getPracticeName();

        $client = new Client($data['accountSid'], $data['smsApiKey']);
        $response = $client->messages->create(
        // Where to send a text message (your cell phone?)
            trim($phone),
            array(
                'from' => trim($twilio_number),
                'messagingServiceSid' => $messagingServiceSid,
                'body' => $message . ' ' . $practice,
                'statusCallback' => self::buildWebHookUrl()
            )
        );
        file_put_contents('/var/www/html/logging/send_response.log', print_r($response, true));
        $save = new StoreTexts();
        $save->saveOutbound($phone, $message);
        return json_encode(
            [
                "success" => true,
                "textId" => time(),
                'quotaRemaining' => 'unlimited',
            ]
        );
    }

    /**
     * @return false|string
     */
    private static function getKey(): string
    {
        $key = new CryptoGen();
        return $key->decryptStandard($GLOBALS['texting_enables']);
    }

    private static function getPracticeName(): string
    {
        $sql = "SELECT facility FROM users WHERE id = ?";
        $result = sqlQuery($sql, [$_SESSION['authUserID']]);
        return $result['facility'];
    }

    public static function buildWebHookUrl(): string
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $http = "https://";
        } else {
            $http = "http://";
        }
        return $http . $_SERVER['HTTP_HOST'] . $GLOBALS['webroot'] .
            '/interface/modules/custom_modules/text-messaging-app/public/api/reply.php?site=' . $_SESSION['site_id'] . '&pid=' . $_SESSION['pid'];
    }

}
