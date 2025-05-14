<?php

/*
 * @package OpenEMR
 *
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * @license "All rights reserved"
 */

namespace Juggernaut\Modules\Jitsi8x8Telehealt;

class BuildRoom
{
    public function uniquePatientRoom(): bool|array|null
    {
        return sqlQuery("SELECT `uuid` FROM `patient_data` WHERE `pid` = ?", [$_SESSION['pid']]);
    }
    public function createMeetingId(): string
    {
        $newmeetingid = sqlQuery("select DOB from patient_data where pid = ?", [$_SESSION['pid']]);
        return md5($newmeetingid['DOB'] . $_SESSION['pid']);
    }

    public function getPatientInfo($pid): bool|array|null
    {
        return sqlQuery("SELECT `fname`, `lname`, `email` from `patient_data` WHERE `pid` = ?", [$pid]);
    }
}
