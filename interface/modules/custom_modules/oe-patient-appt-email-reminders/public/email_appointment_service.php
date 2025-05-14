<?php

/**
 * package    Patient Reminder Module
 *  link      https://affordablecustomehr.com
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 */

require_once(dirname(__FILE__,2) . '/src/ApiDispatcher.php');
require_once(dirname(__FILE__,5) . '/globals.php');

use Juggernaut\Modules\ApiDispatcher;
function start_email_reminders(){
	ApiDispatcher::sendBatchRequest();
}