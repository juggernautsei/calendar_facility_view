<?php

/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

namespace Juggernaut\OpenEMR\Modules\GoogleCalendar\Controllers;

use Google\Client;
use Google\Service\Calendar;
use Juggernaut\OpenEMR\Modules\GoogleCalendar\Services\CredentialService;
class GoogleClient
{
    public function __construct()
    {
        $client = new Client;
        $client->setAuthConfig($clientSecret);
        $client->setRedirectUri($redirect_uri);
        $client->addScope(Google\Service\Calendar::CALENDAR);
    }
    public function getEvents()
    {
        $list = $service->events->listEvents('primary');
        return $list;
    }


}
