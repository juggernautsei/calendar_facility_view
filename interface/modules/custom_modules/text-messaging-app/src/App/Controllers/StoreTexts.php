<?php

/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut\Text\Module\App\Controllers;

use OpenEMR\Common\Database\QueryUtils;
use PDO;
use PDOException;
use Juggernaut\Text\Module\App\Services\UpdateCalendarStatus;
class StoreTexts
{
    private PDO $pdo;

    /**
     * @param array $response
     * @param string|null $db
     * @return void
     */
    public function saveText(array $response): void
    {
        $site = $_SESSION['site_id'];
        $pid = $_SESSION['pid'];
        $db = '';
        if (isset($site)) {
            $db = $this->retrieveDb($site);
        }

        $this->pdoConn($site);
        $statement = sprintf(
            "INSERT INTO `%s`.`text_message_module` (`id`, `provider_id`, `fromnumber`, `text`, `date`) VALUES (NULL, ?, ?, ?, NOW())",
            $db
        );
        //update calendar status if confirmed is in the message
        if (stripos($response['Body'], 'c') !== false || stripos($response['Body'], 'x') !== false) {
            $appointmentUpdate = new UpdateCalendarStatus();
            $appointmentUpdate->data = $response;
            $appointmentUpdate->status = $response['text'];
            $appointmentUpdate->updateCalendarStatus();
        }
        $binding = [
            'Patient',
            $response['From'],
            $response['Body']
        ];
        QueryUtils::sqlInsert($statement, $binding);

    }
    public function saveOutbound($number, $message): void
    {
        $binding = [$_SESSION['authUser'], "+1".$number, $message];
        $statement = "INSERT INTO `text_message_module` (`id`, `provider_id`, `fromnumber`, `text`, `date`) VALUES (NULL, ?, ?, ?, NOW())";
        QueryUtils::sqlInsert($statement, $binding);
    }
    private function retrieveDb($site): string
    {
        global $sqlconf;
        require_once dirname(__FILE__, 8) . "/sites/" . $site . "/sqlconf.php";
        return text($sqlconf["dbase"]);
    }
    public function pdoConn($site): void
    {
        global $sqlconf;
        require_once dirname(__FILE__, 8) . "/sites/" . $site . "/sqlconf.php";

        try {
            $this->pdo = new PDO(
            "mysql:host=localhost;dbname={$sqlconf['dbase']}",
            $sqlconf["login"],
            $sqlconf['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] // Enable exceptions for errors
            );
        } catch (PDOException $e) {
            error_log( 'Connection failed: ' . $e->getMessage());
        }
    }
}
