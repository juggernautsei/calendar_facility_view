<?php

namespace Juggernaut\Text\Module\App\Services;

use OpenEMR\Services\Globals\GlobalSetting;

class Globals
{
    public function createTextMessageGlobals($event): void
    {
        define("TEXT_MESSAGING", "Text Messaging");
        $instruct = xl('Obtain API Key to send messages');
        $event->getGlobalsService()->createSection(TEXT_MESSAGING, "Report");
        $setting = new GlobalSetting(xl('TextBelt API Key'), 'encrypted', '', $instruct);
        $event->getGlobalsService()->appendToSection(TEXT_MESSAGING, "texting_enables", $setting);

        $api_key = xl('Obtain API Key');
        $key_settings = new GlobalSetting(xl('Reply API Key'), 'encrypted', '', $api_key);
        $event->getGlobalsService()->appendToSection(TEXT_MESSAGING, "response_key", $key_settings);

        $enableApptReminders = xl('Enable Appt Reminders');
        $apptReminder = new GlobalSetting(xl('Enable Appt Reminders'), 'bool', '', $enableApptReminders);
        $event->getGlobalsService()->appendToSection(TEXT_MESSAGING, 'enable_appt_reminders', $apptReminder);

    }
}
