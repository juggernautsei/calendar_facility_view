<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

namespace Juggernaut\Module\PatientCheckin\Services;

class ModuleServices
{
    public function isModuleActive()
    {
        $sql = "SELECT `mod_active` FROM `modules` WHERE `mod_directory` = 'oe-module-patient-checkin'";
        $status = sqlQuery($sql);
        return $status['mod_active'];
    }
}
