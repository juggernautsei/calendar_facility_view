<?php

/**
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

namespace Juggernaut\Text\Module;

use OpenEMR\Events\Appointments\AppointmentSetEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AppointmentSubscriber implements EventSubscriberInterface
{


    public static function getSubscribedEvents(): array
    {
        return [
            AppointmentSetEvent::EVENT_HANDLE => 'alertInsuranceCompany'
        ];
    }

    public function alertInsuranceCompany(AppointmentSetEvent $event)
    {
        $appointmentData = $event->givenAppointmentData();
        return new InsuranceNotifications($appointmentData);
    }
}
