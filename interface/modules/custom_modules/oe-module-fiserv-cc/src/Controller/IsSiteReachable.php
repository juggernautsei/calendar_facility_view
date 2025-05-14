<?php
/*
 * @package openemr
 * @link https://www.open-emr.org
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright (c) 2023
 * @license All Rights Reserved
 */

namespace OpenEMR\Modules\Clover\Controller;

use Guzzle\GuzzleHttp\GuzzleException;
use GuzzleHttp\Exception\ClientException;
use Guzzle\Http\Client;
class IsSiteReachable
{
    const URL = 'https://api.clover.com';
    private mixed $site;

    public function __construct($site)
    {
        $this->site = $site;
    }

    public function isSiteReachable()
    {
        // Initialize cURL session
        $ch = curl_init($this::URL . $this->site);

        // Set cURL options
        curl_setopt($ch, CURLOPT_HEADER, true);           // We'll just get the header
        curl_setopt($ch, CURLOPT_NOBODY, true);           // We don't need the body content
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   // Return the output as a string
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);            // Set a timeout, e.g., 10 seconds

        // Execute cURL session and get the response
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        file_put_contents('/var/www/html/hook/clover_site_uri.json', $httpcode, FILE_APPEND);
        // Close cURL session
        curl_close($ch);

        // Check the HTTP code, 200 means the website is operational
        //return ($httpcode >= 200 && $httpcode < 400) ? true : false;
    }
}
