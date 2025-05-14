<?php

/**
 * package   OpenEMR
 *  link      http//www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  license   https//github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

namespace Juggernaut\Text\Module\Controllers;

class Database
{
    public static function insuranceCompanies($selectedCompany)
    {
        $companies = [];
        $query = self::companiesQuery();
        $sql = sqlStatement($query);
        while ($iter = sqlFetchArray($sql)) {
            $companies[] = $iter;
        }
        $select = "<select name='icompany' id='icompany' class='select2-search--dropdown'>";
        $select .= "<option></option>";
        foreach ($companies as $company) {
            $select .= "<option value='" . $company['id'] . "'";
            if (!empty($selectedCompany) && $selectedCompany == $company['id']) {
                $select .= ' selected ';
            }
            $select .= ">";
            $select .= $company['name'];
            $select .= "</option>";
        }
        $select .= "</select>";
        return $select;
    }

    public static function firstInsuaranceCompany()
    {
        $query = self::companiesQuery();
        return sqlQuery($query . " ORDER BY ic.id ASC LIMIT 1");
    }

    private function companiesQuery()
    {
        return "SELECT DISTINCT ic.id, ic.name " .
            "FROM insurance_companies AS ic, insurance_data AS ind WHERE ic.id = ind.provider ";
    }
}
