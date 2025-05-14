<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut\Text\Module;

use Mpdf\Mpdf;
use MyMailer;
use PHPMailer\PHPMailer\Exception;

class InsuranceNotifications
{
    protected bool $checkInsurance;
    protected $contact;
    protected $letter;
    protected $pid;
    protected $pdf;
    protected int $size;
    protected array $statuses;
    protected $tempFilename;
    protected $output;
    /**
     * @param array $appointmentData
     */
    public function __construct(array $appointmentData)
    {
        $this->pid = $appointmentData['form_pid'];
        $this->checkInsurance = Database::isPatientTriWest($this->pid);
        $this->contact = Database::vaContactName($this->pid);
        $document = new TemplateProcessor($appointmentData);
        $flag = $document->createTemplateFlag();
        if ($this->checkInsurance && !empty($this->contact[1]['field_value']) && $flag) {
             //fill out template if the contact form has an email address
            $this->letter = $document->letterTemplate();
        }

        $this->storeTempPdfDocument();
    }

    protected function storeTempPdfDocument(): void
    {
        require_once dirname(__DIR__, 5) . "/globals.php";
        require_once dirname(__DIR__, 6) . "/controllers/C_Document.class.php";
        require_once dirname(__DIR__, 6) . "/library/documents.php";

        $fileName = $this->pid . "-" . date('Y-m-d_H:m:s') . ".html";
        $tmpdir = $GLOBALS['OE_SITE_DIR'] . '/documents/temp/';
        $this->tempFilename = $tmpdir . $fileName;
        file_put_contents($this->tempFilename, $this->letter);
        $this->size = filesize($this->tempFilename);
        $type = "application/html";
        $category_id = 693414;

        addNewDocument($fileName, $type, $this->tempFilename, 0, $this->size, $_SESSION['authUserID'], $this->pid, $category_id);
        $this->faxVaDocument(); //send fax if a fax module is installed
        $this->emailVaDocument();
        unlink($this->tempFilename);
    }

    protected function faxVaDocument(): void
    {

        //check for a fax module
        $moduleType = Database::isFaxable();
        if ($moduleType == 'FaxSMS') {
            $sendFaxUrl = "https://" . $_SERVER['HTTP_HOST'] . '/interface/modules/custom_modules/oe-module-faxsms/contact.php?isDocuments=false&isQueue=' .
            $this->contact[2]['field_value'] . '&file=' . $this->tempFilename;
            $opts = [ 'https' => [
                'header' => [ "Content-type: application/x-www-form-urlencoded\r\n"
                    . "Content-Length: " . strlen($this->size) . "\r\n",
                     'content' => $this->tempFilename
                ],
                'method' => 'POST'
            ]];
            $context = stream_context_create($opts);
            $content = fopen($sendFaxUrl, 'r', false, $context);
        }
        if ($moduleType == 'Documo') {
            //do this later
        }
    }

    /**
     * @throws Exception
     */
    protected function emailVaDocument()
    {
        if (!empty($this->contact[1]['field_value'])) {
            $email = new MyMailer();

            $message = xlt('This email is to notify your office of a change in the patient appointment status');
            $email_subject = xl('Patient Appointment Status Change');
            $email_sender = $GLOBALS['patient_reminder_sender_email'];
            $email->AddReplyTo($email_sender, $email_sender);
            $email->SetFrom($email_sender, $email_sender);
            $email->AddAddress($this->contact[1]['field_value'], $this->contact[1]['field_value']);
            $email->AddAttachment($this->tempFilename, 'PatientAppointmentStatus.html');
            $email->Subject = $email_subject;
            $email->MsgHTML("<html><body><div class='wrapper'>" . $message . "</div></body></html>");

            $email->IsHTML(true);
            $email->AltBody = $message;

            if ($email->Send()) {
                return true;
            } else {
                $email_status = $email->ErrorInfo;
                error_log("EMAIL ERROR: " . errorLogEscape($email_status), 0);
                return false;
            }
        }
    }
}
