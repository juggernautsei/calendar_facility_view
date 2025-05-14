<?php
/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut\Text\Module;

class TemplateProcessor
{
    protected $auth;
    protected string $apptStatus;
    protected $template;
    protected string $title;
    protected string $pid;
    protected array $data;
    protected array $status;
    public string $contactEmail;
    public string $contactFax;


    public function __construct($appointmentData)
    {
        $this->status = ['+', '?', 'x'];
        $this->data = $appointmentData ?? null;
        $this->pid = $appointmentData['form_pid'] ?? null;
        $this->title = $appointmentData['form_title'] ?? null;
        $this->apptStatus = $appointmentData['form_apptstatus'] ?? null;

    }

    public function createTemplateFlag()
    {
        if (in_array($this->apptStatus, $this->status)) {
            return true;
        } else {
            return false;
        }
    }

    public function letterTemplate()
    {
        $this->template = self::getLetterTemplate(); // retrieve template contents
        $this->auth = self::getTemplateData(); //auth number
        return self::mergeDataIntoTemplate();
    }

    protected function getLetterTemplate()
    {
        $isFirstAppointment = Database::countAppointments($this->pid);
        if ($isFirstAppointment['previous'] > 0) {
            $this->template = dirname(__FILE__) . "/../Templates/Letter-head-template.html";
        } else {
            $this->template = dirname(__FILE__) . "/../Templates/First-appointment-template.html";
        }
        return file_get_contents($this->template);
    }

    protected function getTemplateData()
    {
        $patientData = new Database();
        return $patientData->lookUpPatientData($this->pid);
    }

    protected function mergeDataIntoTemplate()
    {
        $s = $this->template;
        $status = self::convertStatus() ?? null;
        $contactName = Database::vaContactName($this->data['form_pid']) ?? null;

        if (in_array($this->data['form_apptstatus'], $this->status)) {
            $s = str_replace("{{APPSTATUS}}", $status, $s);
            $s = str_replace("{{VeteranVAAuthorizationnumber}}", $this->auth, $s);
            $s = str_replace("{{VeteranContactFirstName}}", $contactName[0]['field_value'], $s);
            $s = str_replace("{{PatientfirstnameLastName}}", $this->data['form_patient'], $s);
            $s = str_replace("{{NewPatientTelehealthappointmentdate}}", $this->data['form_date'], $s);
            $s = str_replace("{{appointmenttime}}", $this->data['form_hour'] .":". $this->data['form_minute'],$s);
            file_put_contents("/var/www/html/errors/filled.txt", $s);

            $this->contactEmail = $contactName[1]['field_value'];
            $this->contactFax = str_replace("-", "", $contactName[2]['field_value']);
            return $s;
        }
    }

    protected function convertStatus()
    {
        switch ($this->data['form_apptstatus']) {
            case '+':
                return 'Rescheduled';

            case '?':
                return 'No Show';

            case 'x':
                return 'Canceled';

            default:
                return null;
        }
    }
}
