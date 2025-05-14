<?php

/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

namespace Juggernaut\OpenEMR\Modules\GoogleCalendar\Services;

use Google\Client;
class TokenServices
{
    public mixed $token;

    public function __construct()
    {
    }
    public function saveToken()
    {
        if (empty($this->token['access_token'])) {
            return false;
        }
        $exitingToken = $this->getToken();
        if (empty($exitingToken['access_token'])) {
            return $this->insertToken();
        } else {
            return $this->updateToken();
        }
    }
    private function insertToken()
    {
        $sql = "INSERT INTO `module_google_calendar_token` (`access_token`, `refresh_token`, `expires_in`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?)";
        $values = array(
            $this->token['access_token'],
            $this->token['refresh_token'],
            $this->token['expires_in'],
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        );
        return sqlStatementNoLog($sql, $values);
    }

    private function updateToken()
    {
        $sql = "UPDATE `module_google_calendar_token` SET `access_token` = ?, `refresh_token` = ?, `expires_in` = ?, `updated_at` = ? WHERE `id` = ?";
        $values = array(
            $this->token['access_token'],
            $this->token['refresh_token'],
            $this->token['expires_in'],
            date('Y-m-d H:i:s'),
            $this->token['id']
        );
        return sqlStatementNoLog($sql, $values);
    }

    public function getToken()
    {
        $sql = "SELECT * FROM `module_google_calendar_token` ORDER BY `id` DESC LIMIT 1";
        return sqlQuery($sql);
    }

    public function getCredentials()
    {
        $sql = "SELECT * FROM `module_google_calendar_credentials` ORDER BY `id` DESC LIMIT 1";
        return sqlQuery($sql);
    }

    public function isTokenStillValid()
    {
        $clientId = $this->getCredentials();
        $token = $this->getToken();
        $client = new Client([
            'client_id' => $clientId['client_id'],
            'client_secret' => $clientId['client_secret']
        ]);
        $payload = $client->verifyIdToken($token['access_token']);
        if ($payload) {
            $userid = $payload['sub'];
            return "Token is valid. User ID: " . $userid;
        } else {
            return "Invalid token.";
        }
    }
    private function refreshToken($existingToken)
    {
    }
}
