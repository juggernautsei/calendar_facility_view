<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut\Text\Module;

class Database
{
    public function lookUpPatientData($pid)
    {
        $val = sqlStatement('select 1 from `module_prior_authorizations` LIMIT 1');
        if ($val !== FALSE) {
            $auth_num = sqlQuery("SELECT `auth_num` FROM `module_prior_authorizations` WHERE `pid` = ? AND `end_date` > NOW()", [$pid]);
            return $auth_num['auth_num'];
        } else {
            return null;
        }
    }

    public static function vaContactName($pid): array
    {
        $contactInfo = [];
        $sql = "SELECT `lbt_data`.`field_value` FROM `lbt_data` " .
            "JOIN `transactions` ON `transactions`.`id` = `lbt_data`.`form_id` " .
            "WHERE `transactions`.`id` = `lbt_data`.`form_id` " .
            "AND (`lbt_data`.`field_id` = 'VAContact' OR `lbt_data`.`field_id` = 'VAPhone' OR `lbt_data`.`field_id` = 'VAEmail')" .
            "AND `transactions`.`pid` = ? " .
            "ORDER BY `transactions`.`id` DESC";
        $vaInfo = sqlStatement($sql, [$pid]);
         while ($row = sqlFetchArray($vaInfo)) {
             $contactInfo[] = $row;
         }

        return $contactInfo;
    }

    public static function isPatientTriWest($pid)
    {
        $ins_name = "SELECT ic.id FROM insurance_companies ic " .
            "JOIN insurance_data isd ON ic.id = isd.provider " .
            "WHERE isd.pid = ?";
        $match = sqlQuery($ins_name, [$pid]);

        if ($match['id'] == 133) {
            return true;
        } else {
            return false;
        }
    }

    public static function countAppointments($pid)
    {
        return sqlQuery("SELECT count(*) AS previous FROM openemr_postcalendar_events WHERE pc_pid = ?", [$pid]);
    }

    public static function isFaxable()
    {
        $module = sqlQuery("SELECT `mod_name` FROM `modules` WHERE `mod_name` LIKE 'FaxSMS%'");
        if (! empty($module['mod_name'])) {
            return 'FaxSMS';
        }
        $module = sqlQuery("SELECT `mod_name` FROM `modules` WHERE `mod_name` LIKE 'Documo%'");
        if (! empty($module['mod_name'])) {
            return 'Documo';
        }
        return null;
    }
}
