<?php

require_once dirname(__DIR__) . '/globals.php';

function getPrimaryPlanName($pid)
{
    return sqlQuery("SELECT `id`, `plan_name`, `copay` FROM `insurance_data` WHERE `pid` = ? AND `type` = 'primary' AND `plan_name` != ''", [$pid]);
}
