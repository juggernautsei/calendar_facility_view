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
class RefundPayment
{
    const URL = 'https://api.clover.com';
    private mixed $paytotal;
    private string $reference;

    public function __construct()
    {
        $checkSite = new IsSiteReachable('/v1/refunds');
        $checkSite->isSiteReachable();
        /*if (!$checkSite->isSiteReachable()) {
            error_log("Clover site is not reachable");
            echo "<title>" . xlt('Clover site is not reachable') . "</title>";
            echo "<h1>" . xlt('Clover site is not reachable') . "</h1>";
            echo "<p>" . xlt('Please try again later or contact your Clover rep') . "</p>";
            die;
        }*/
    }

    public function checkIfCloverPayment($paymentId): bool
    {
        $sql = "SELECT `reference`, `pay_total` FROM `ar_session` WHERE `session_id` = ?";
        $result = sqlQuery($sql, [$paymentId]);
        //remove decimal from pay_total
        $this->paytotal = str_replace(".", "", $result['pay_total']);
        $this->reference = substr($result['reference'], 0, -7);
        if (!empty($this->reference)) {
        $result = substr($result['reference'], - 6);
        }
        if ($result == 'Clover') {
            return true;
        }
        return false;
    }

    public function refundPayment()
    {
        $jsonBody = json_encode([
            'charge' => $this->reference,
            'amount' => $this->paytotal,
            'external_reference_id' => '2235',
            'reason' => 'requested_by_customer',
        ]);
file_put_contents('/var/www/html/hook/clover_refund.json', $jsonBody);
        $client = new \GuzzleHttp\Client();
        $oauth2 = new CloverConnectController();
        $oauth2_token = $oauth2->getAppCredentials();
        try {
            $response = $client->request('POST', $this::URL . '/v1/refunds', [
                'body' => $jsonBody,
                'headers' => [
                    'accept' => 'application/json',
                    'authorization' => 'Bearer ' . $oauth2_token['app_secret'],
                    'content-type' => 'application/json',
                ],
            ]);
        } catch (\GuzzleHttp\ClientException $e) {
            if ($e->hasResponse()) {
                if ($e->getResponse()->getStatusCode() == 401) {
                    return xlt("Unauthorized");
                } elseif ($e->getResponse()->getStatusCode() == 404) {
                    return xlt("Not Found");
                } elseif ($e->getResponse()->getStatusCode() == 500) {
                    return xlt("Internal Server Error");
                } else {
                    return "An error occurred: " . $e->getResponse()->getStatusCode();
                }
            }
        }
        $finished = json_decode($response->getBody(), true);
        return $finished;
    }
}
