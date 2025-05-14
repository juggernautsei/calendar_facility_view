<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

$ignoreAuth = true;
$sessionAllowWrite = true;
require_once dirname(__DIR__, 4) . "/globals.php";
require_once dirname(__DIR__, 5) . "/library/documents.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['image'])) {
    $imageData = $_POST['image'];
    $fileName = $_POST['type'];

    // Decode base64 image
    $image = str_replace('data:image/png;base64,', '', $imageData);
    $image = str_replace(' ', '+', $image);
    $imageData = base64_decode($image);

    // Define upload directory. This needs to be changed to the correct directory
    $uploadDir = dirname(__DIR__, 5) . "/sites/" . $_SESSION['site_id'] . "/documents/temp/";
    if (!is_dir($uploadDir)) {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(["status" => "error", "message" => "Upload directory " . $uploadDir . " not found"]);
        exit();
    }
    try {
        file_put_contents($uploadDir . $fileName . "_" . $_POST['pid'] . "_" . time() . ".png" , $imageData);
    } catch (Exception $e) {
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(["status" => "error", "message" => "Image upload failed because " . $uploadDir . " is not writable"]);
        exit();
    }

    echo json_encode(["status" => "success", "message" => "Image uploaded successfully " . $_POST['type']]);

    $name = $fileName . "_" . $_POST['pid'] . "_" . time() . ".png";
    $type = "image/png";
    $tmp_name = $uploadDir . $name;
    $error = 0;
    $size = filesize($tmp_name);

    // Save the file
    addNewDocument(
        $name,
        $type,
        $tmp_name,
        $error,
        $size,
        $owner = 'Patient',
        $patient_id_or_simple_directory = "00",
        $category_id = '4',
        $higher_level_path = '',
        $path_depth = '1',
        $skip_acl_check = true
    );
    //remove file
    //unlink($tmp_name);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
