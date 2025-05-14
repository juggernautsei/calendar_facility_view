<?php


/**
 * package    Patient Reminder Module
 *  link      https://affordablecustomehr.com
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 */

$ignoreAuth = true;
$sessionAllowWrite = true;
//INCLUDES, DO ANY ACTIONS, THEN GET OUR DATA
require_once(dirname(__FILE__,5) . '/globals.php');
require_once("$srcdir/registry.inc");
require_once(dirname(__FILE__,2) . '/src/Database.php');
use OpenEMR\Common\Acl\AclMain;
use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;
use Juggernaut\Modules\Database;

if (!empty($_POST['form_action']) && ($_POST['form_action'] == 'process')) {
    $params = array();
    if ($_POST['app_s'] != 0 and $_POST['app_s'] != '') {
        array_push($params, $_POST['app_s']);
    }

    if ($_POST['app_e'] != 0 and $_POST['app_e'] != '') {
        array_push($params, $_POST['app_e']);
    }
    $res = Database::getAppointmentEmailReminderDetails($params);

    require_once('batchEmail.php');
}
?>