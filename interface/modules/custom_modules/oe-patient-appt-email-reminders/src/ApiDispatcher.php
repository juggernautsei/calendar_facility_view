<?php

/**
 * package    Patient Reminder Module
 *  link      https://affordablecustomehr.com
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  All rights reserved
 */

namespace Juggernaut\Modules;

require_once(dirname(__FILE__,5) . '/globals.php');

use Juggernaut\Modules\Database;
use OpenEMR\Common\Csrf\CsrfUtils;

global $EMAIL_NOTIFICATION_HOUR;
class ApiDispatcher
{
    public function __construct()
    {
    }
    public static function sendBatchRequest(){
        global $EMAIL_NOTIFICATION_HOUR;
        
        //We need a way to calculate number of days
        $nDays = self::numberOfDays();
        //What do you think of this? Then we can do away with the if
        $date = date("Y-m-d",strtotime($nDays));

        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')  
            $url = "https://";   
        else  
            $url = "http://";  
        $url .= $_SERVER['HTTP_HOST'];
//The start date and end date should be the same day. 

        $url .= "/interface/modules/custom_modules/oe-patient-appt-email-reminders/public/batchcom.php";
        $fields=array(
            'csrf_token_form' => CsrfUtils::collectCsrfToken(),
            'form_action' => 'process',
            'app_s' => $date,
            'app_e' => $date,
        );
        $curl =  curl_init();
        curl_setopt_array($curl, array( 
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $fields
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    private function numberOfDays()
    {
        global $EMAIL_NOTIFICATION_HOUR;
        $days = round($EMAIL_NOTIFICATION_HOUR/24);
        //the idea is to be flexible up to 5 days

        switch ($days) {
            case 1:
            $numDays = '+1 days';
            break;

            case 2:
            $numDays = '+2 days';
            break;

            case 3:
            $numDays = '+3 days';
        }
        return $numDays;
    }
}
