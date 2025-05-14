<?php

/**
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *     All rights reserved
 */

namespace Juggernaut\Module\HospitalCharge\App\Model;

use OpenEMR\Services\FacilityService;
class HomeModel
{
    private mixed $webroot;

    public function __construct()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $this->webroot = $protocol . $_SERVER["HTTP_HOST"];
    }

    private function getFacilities()
    {
        $facilityService = new FacilityService();
        return $facilityService->getAllFacility();
    }
    public function getHomeData()
    {
        $facilities = $this->getFacilities();
        return [
            'title' => 'Rapid Charge',
            'content' => 'This is the hospital charge module',
            'facilities' => $facilities,
            'webroot' => $this->webroot,
            'current_page' => 'home',
        ];
    }
}
