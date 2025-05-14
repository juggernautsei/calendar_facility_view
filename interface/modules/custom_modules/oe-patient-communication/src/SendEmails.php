<?php

namespace src;

use OpenEMR\Common\Crypto\CryptoGen;

class SendEmails
{
    public $messageSubject;
    public function __construct()
    {
        $this->messageSubject = $_POST['subject'];
        $this->sendEmails();
    }

    public function sendEmails()
    {
        $email = $GLOBALS['patient_reminder_sender_email'];
        $user = $GLOBALS['SMTP_USER'];
        $cryptoGen = new CryptoGen();
        $password = $cryptoGen->decryptStandard($GLOBALS['SMTP_PASS']);
        $port = $GLOBALS['SMTP_PORT'];
        $secure = $GLOBALS['SMTP_SECURE'];
        $host = $GLOBALS['SMTP_HOST'];
        $refId = "0092";
        $body = "<table><tr><td ><strong>" .
            $GLOBALS['practice_name'] .
            "</strong></td></tr><tr><td><p>" .
            $this->messageSubject .
            "</p>
                </td>
                </tr>
                <tr>
                <td>Email</td><td>$email</td>
                </tr>
                </table>";

        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = true;
            $mail->isSMTP();
            $mail->IsHTML(true);
            $mail->Host = $host;
            $mail->SMTPAuth = true;
            $mail->Username = $user;
            $mail->Password = $password;
            $mail->SMTPSecure = $secure;
            $mail->Port = $port;
            echo $GLOBALS['patient_reminder_sender_email'] . '<br>';

            $mail->setFrom($GLOBALS['patient_reminder_sender_email'], 'Billing Coordinator');

            $mail->addAddress($email, 'Client');
            $mail->Subject = 'Testing outbound emails ';
            $mail->Body = $body;
            $mail->send();
            echo '<br><br>Message Sent. Please check email for results';
            die('after sending');
        } catch (\Exception $e) {
            echo "Message could not be sent";
            echo "<pre>";
            echo "Mailer error: " . $mail->ErrorInfo;
        }
    }

    private function getClinicInformation()
    {
        $clinicInfo = array();
        $clinicInfo['clinicName'] = $GLOBALS['practice_name'];
        $clinicInfo['clinicAddress'] = $GLOBALS['practice_street_address'];
        $clinicInfo['clinicCity'] = $GLOBALS['practice_city'];
        $clinicInfo['clinicState'] = $GLOBALS['practice_state'];
        $clinicInfo['clinicZip'] = $GLOBALS['practice_zip'];
        $clinicInfo['clinicPhone'] = $GLOBALS['practice_phone'];
        $clinicInfo['clinicFax'] = $GLOBALS['practice_fax'];
        $clinicInfo['clinicEmail'] = $GLOBALS['practice_email'];
        $clinicInfo['clinicWebsite'] = $GLOBALS['practice_website'];
        return $clinicInfo;
    }
}
