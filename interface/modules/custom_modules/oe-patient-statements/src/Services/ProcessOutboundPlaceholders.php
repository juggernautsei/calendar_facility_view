<?php

/*
 *   @package   OpenEMR
 *   @link      http://www.open-emr.org
 *
 *   @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *   Copyright (c)  Juggernaut Systems Express
 *   @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *   The purpose of this class is to get a list of all the databases and their credentials
 *
 */

namespace Juggernaut\Module\PatientStatements\Services;

class ProcessOutboundPlaceholders
{
    public function __construct()
    {
    }
    public function replacePlaceholders($balance, $clinic, $dos): string
    {
        $message = $this->getStoredMessage();
        $message = str_replace('{balance}', $balance, $message);
        $message = str_replace('{clinic}', $clinic, $message);
        $message = str_replace('{dos}', $dos, $message);
        return $message;
    }

    public function getStoredMessage(): string
    {
        $message = sqlQuery("SELECT `statement` FROM `module_patient_statement_settings` ORDER BY `id` DESC LIMIT 1");
        return $message['statement'] ?? '';
    }

    public function saveMessage($message): void
    {
        $sql = "INSERT INTO `module_patient_statement_settings` (`statement`, `user`) VALUES (?, ?)";
        sqlStatement($sql, [$message, $_SESSION['authUser']]);
    }
}
