<?php

/*
 * package   OpenEMR
 * link           https://open-emr.org
 * author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

namespace Juggernaut\Quest\Module;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use ZipArchive;
use Juggernaut\Quest\Module\Services\ImportCompendiumData;

class QuestGetCommon
{
    private string $tmpDir;

    public function __construct()
    {
        $this->tmpDir = dirname(__DIR__, 5) . '/sites/' . $_SESSION['site_id'] . '/documents/temp/';
    }

    final public function getRequestToQuest(
        $resourceLocation
    ): string
    {
        $token = new QuestToken();
        $postToken = json_decode($token->getFreshToken(), true);
        $postToken = $postToken['access_token'] ?? '';
        $mode = $token->operationMode() ?? '';
        $curl = curl_init();
        if (!empty($mode) && !empty($resourceLocation) && !empty($postToken)) {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $mode . $resourceLocation,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer " . $postToken
                ),
            ));
        } else {
            error_log(" Quest Lab Order:Debug location " . $mode . $resourceLocation);
        }
        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        curl_close($curl);
        if ($status == 200) {
            return $response;
        } else {
            return "HTTP Status Code: " . $status;
        }
    }

    final public function retrieveCompendium(
        $fileName,
        $retrieveURILocation
    ): string
    {
        // Create a Guzzle client
        $client = new Client();

        $token = new QuestToken();
        $postToken = json_decode($token->getFreshToken(), true);
        $postToken = $postToken['access_token'] ?? '';
        $mode = $token->operationMode() ?? '';

        // Path where you want to save the downloaded file
        $tmpDir = dirname(__DIR__, 5) . '/sites/' . $_SESSION['site_id'] . '/documents/temp/';
        $saveTo = $tmpDir . $fileName;

        try {
            // Set headers with the token
            $headers = [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36',
                'Authorization' => 'Bearer ' . $postToken,
                "Accept: */*",
                "Accept-Encoding: gzip, deflate, br"
            ];

            // Send a GET request with sink option for download
            $response = $client->get($mode.$retrieveURILocation, ['headers' => $headers, 'sink' => $saveTo]);

            // Check for successful response
            if ($response->getStatusCode() === 200) {
                $unzipResults = $this->unzipCdcFile($fileName);
                $successMessage = xlt("File downloaded successfully imported into database: ");
                if ($unzipResults) {
                    new ImportCompendiumData();
                    unlink($this->tmpDir . $fileName);
                    return "<span class='text-success'><strong>" . $successMessage . "</strong></span>" . $fileName;
                } else {
                    $failureMessage = xlt("Error unzipping file");
                    return "<span class='text-danger'><strong>" . $failureMessage . "</strong></span>";
                }
            } else {

                return "Error downloading file. Status code: " . $response->getStatusCode();
            }
        } catch (GuzzleException $e) {
           return "An error occurred: " . $e->getMessage();
        }
    }
    private function unzipCdcFile($fileName): bool
    {
        #unpack file into the temp directory for import to database
        //$tmpDir = dirname(__DIR__, 5) . '/sites/' . $_SESSION['site_id'] . '/documents/temp/';
        $zip = new ZipArchive;
        $res = $zip->open($this->tmpDir . $fileName);
        if ($res === TRUE) {
            $zip->extractTo($this->tmpDir);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }
}
