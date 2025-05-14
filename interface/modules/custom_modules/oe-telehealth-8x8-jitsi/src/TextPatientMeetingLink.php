<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * All rights reserved
 */

namespace Juggernaut\Modules\Jitsi8x8Telehealth;

require_once dirname(__DIR__, 4) . "/text-messaging-app/src/Controllers/SendTelemedicineMessage.php";

use Juggernaut\Text\Module\App\Controllers\SendTelemedicineMessage;
class TextPatientMeetingLink
{
    private mixed $patient_cellphone;
    const MODULE_PATH = '/interface/modules/custom_modules/oe-telehealth-8x8-jitsi/public/patient.php';
    public function __construct($patient_cellphone)
    {
        $this->patient_cellphone = $patient_cellphone;
    }

    public function textLink(): void
    {
        $message = $this->buildMessage();
            $link = new SendTelemedicineMessage();
            $link->outBoundTelemedicineMessage($this->patient_cellphone, $message);
    }

    private function buildMessage(): string
    {
        $url = "https://". $_SERVER['SERVER_NAME'];
        return "Click here to join telehealth session " . $url . self::MODULE_PATH . "?cellphone=" . $this->patient_cellphone;
    }
}
