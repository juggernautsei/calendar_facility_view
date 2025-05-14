<?php
/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

namespace Juggernaut\OpenEMR\Modules\GoogleCalendar\Services;

class CredentialService
{

    public mixed $client_id;
    public mixed $client_secret;
    public mixed $redirect_uri;

    public function saveCredential()
    {
        $sql = "INSERT INTO `module_google_calendar_credentials` (`client_id`, `client_secret`, `redirect_uri`) VALUES (?, ?, ?)";
        $values = array(
            $this->client_id,
            $this->client_secret,
            $this->redirect_uri
        );
        return sqlStatementNoLog($sql, $values);
    }

    public function getCredentials()
    {
        $sql = "SELECT * FROM `module_google_calendar_credentials`";
        return sqlQuery($sql);
    }
}
