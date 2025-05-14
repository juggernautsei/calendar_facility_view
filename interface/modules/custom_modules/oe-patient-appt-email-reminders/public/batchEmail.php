<?php
set_time_limit(1500);

/**
 * package    Patient Reminder Module
 *  link      https://affordablecustomehr.com
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 */

require_once(dirname(__FILE__,5) . '/globals.php');
require_once(dirname(__FILE__,6) . '/modules\sms_email_reminder\cron_functions.php');
require_once(dirname(__FILE__,2) . '/src/Database.php');

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
use PHPMailer\PHPMailer\PHPMailer;
use OpenEMR\Common\Crypto\CryptoGen;
use Juggernaut\Modules\Database;

/**
 * @var string $res
 *
 */
$email_sender =  $GLOBALS['SMTP_USER'];
$cryptoGen = new CryptoGen();
$password = $cryptoGen->decryptStandard($GLOBALS['SMTP_PASS']);
while ($row = sqlFetchArray($res)) {

    // prepare text for ***NAME*** tag
    $pt_name = $row['title'] . ' ' . $row['fname'] . ' ' . $row['lname'];
    $pt_email = $row['email'];
    $email_subject = "Testing Appointment Reminder";
    $provider = Database::getProviderDetail($row['providerID']);
    $email_body = "<h3>Hello,<br> Dear ***NAME***,</h3>".
            "<i>This is <b>" . $provider['username'] . "</b> reminding you about your scheduled tomorrow at</i> " . $row['appt_start_time'] . "<i> Call/text us if you need to reschedule. See you then! </i>".
            "<br> this email is only Testing email So, you can ignore this mail do not reply".
            "<br><br><br><h3 style='color:red'>Sorry for inconvenience.<h3>".
            "<br><h3>Thanks</h3>";

    $email_subject = preg_replace('/\*{3}NAME\*{3}/', $pt_name, $email_subject);
    $email_body = preg_replace('/\*{3}NAME\*{3}/', $pt_name, $email_body);

    if($pt_email){
        $mail =  new PHPMailer();

        $mail->IsSMTP();
        $mail->SMTPDebug = 1;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure =$GLOBALS['SMTP_SECURE']; 
        $mail->Host = $GLOBALS['SMTP_HOST']; 
        $mail->Port = $GLOBALS['SMTP_PORT']; 
        $mail->IsHTML(true);
        $mail->Username = $GLOBALS['SMTP_USER'];
        $mail->Password = $password;
        $mail->setFrom($email_sender);
        $mail->addAddress($pt_email, $pt_name);
        $mail->addReplyTo($email_sender);
        $mail->isHTML(true);
        $mail->Subject = $email_subject;
        $mail->Body    =  $email_body;
        if(!$mail->send()){
            echo "Mailer Error: " . $mail->ErrorInfo . "<br>";
        } else {
            error_log("email send to $pt_name $pt_email");
            error_log("Provider: " . $provider['username']);
        }
        $db_email_msg['type'] = "Email Reminder";
        $db_email_msg['email_sender'] = $email_sender;
        $db_email_msg['email_subject'] = $email_subject;
        $db_email_msg['message'] = $email_body;
        $db_email_msg['sms_gateway_type'] = "sendgrid";

        cron_InsertNotificationLogEntry("Email Reminder",$row,$db_email_msg);
    }
}
?>