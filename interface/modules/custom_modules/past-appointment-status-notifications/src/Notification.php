<?php

/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut;

use MyMailer;
use PHPMailer\PHPMailer\Exception;


class Notification
{
    private $pendingArray;

    /**
     * @throws \phpmailerException
     * @throws Exception
     */
    public function sendAlert($days, $contact)
    {
        $listPending = new NotificationModel($days, $contact);
        $providerEmail = $listPending->getProviderEmailAddress($contact);
        $this->pendingArray = $listPending->hasPendingAppts();
        if (is_array($this->pendingArray)) {
            $providerMessage = $this->buildMessage();
            return $this->emailProvider($providerMessage, $providerEmail);
        }
        return;
    }

    private function buildMessage()
    {
        $message = '';

        foreach ($this->pendingArray as $appt) {
            $message .= "Patient " . $appt['pc_pid'] . ", " . $appt['pc_eventDate'] . ", " . $appt['pc_startTime'] . "<br>";
        }
        $message .= xlt('Update these patients appointments or risk not being paid. Jana');
        return $message;
    }

    /**
     * @throws Exception
     */
    private function emailProvider($message, $providerEmail): string
    {

        $emailSubject = xlt('Pending Appointment Status');
        $email_sender = $GLOBALS['patient_reminder_sender_email'];
        $mail = new MyMailer();
        $mail->AddReplyTo($email_sender, $email_sender);
        $mail->SetFrom($email_sender, $email_sender);
        $mail->AddAddress($providerEmail);
        $mail->Subject = $emailSubject;
        $mail->MsgHTML($message);
        $mail->IsHTML(false);
        $mail->AltBody = $message;

        if ($mail->Send()) {
            file_put_contents('/var/www/html/errors/appt_notification.txt', "Sent " . date('Y-m-d') . "\r\n", FILE_APPEND);
        } else {
            $mail_status = $mail->ErrorInfo;
            error_log("EMAIL ERROR: " . errorLogEscape($mail_status), 0);
        }
        return "SENT";
    }

    public function updateBackgroundServices($days): void
    {
        $interval = $days * 1440;
        $backfunc = 'start_appt_notification';
        sqlStatement("UPDATE background_service SET exec_interval = ? WHERE function = ?", [$interval, $backfunc]);
    }
}