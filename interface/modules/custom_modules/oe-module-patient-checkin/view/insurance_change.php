<?php

Header("X-Frame-Options: DENY");
Header("Content-Security-Policy: frame-ancestors 'none'");

$ignoreAuth = true;
$sessionAllowWrite = true;
require_once dirname(__DIR__, 4) . "/globals.php";

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;

CsrfUtils::setupCsrfKey();

$_SESSION['pid'] = $_GET['id'];
$_SESSION['patient_checkin'] = true;

$patient_id = isset($_GET['id']) ? $_GET['id'] : '';

$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo xlt("Upload Insurance Card") ?></title>
    <?php Header::setupHeader(); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .upload-section {
            margin: 20px 0;
            text-align: center;
        }
        video, img {
            width: 100%;
            max-width: 300px;
            border-radius: 10px;
            margin-top: 10px;
        }
        .button-group {
            margin-top: 10px;
        }
        .hide {
            display: none;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row text-center">
        <div class="col-md-12">
            <h3><?php echo xlt("Patient Check-In Insurance Update ") ?></h3>
        </div>
    </div>

    <div class="upload-section">
        <h3><?php echo xlt(" Front of Insurance Card") ?></h3>
        <video id="videoFront" autoplay></video>
        <canvas id="canvasFront" class="hide"></canvas>
        <img id="previewFront" class="hide" />
        <div class="button-group">
            <button id="captureFront" class="btn btn-primary"><?php echo xlt("Capture") ?></button>
            <button id="retakeFront" class="btn btn-primary hide"><?php echo xlt("Retake") ?></button>
            <button id="uploadFront" class="btn btn-primary hide"><?php echo xlt("Upload") ?></button>
        </div>
    </div>

    <div class="upload-section">
        <h3><?php echo xlt("Back of Insurance Card") ?></h3>
        <video id="videoBack" autoplay></video>
        <canvas id="canvasBack" class="hide"></canvas>
        <img id="previewBack" class="hide" />
        <div class="button-group">
            <button id="captureBack" class="btn btn-primary"><?php echo xlt("Capture") ?></button>
            <button id="retakeBack" class="btn btn-primary hide"><?php echo xlt("Retake") ?></button>
            <button id="uploadBack" class="btn btn-primary hide"><?php echo xlt("Upload") ?></button>
        </div>
    </div>
    <div class="row text-center">
        <div class="col-md-12">
            <a href="insurance.php?id=<?php echo  $patient_id; ?>" class="btn btn-primary w-25"><?php echo xlt("Back") ?></a>
            <a href="payment.php?id=<?php echo $patient_id; ?>" class="btn btn-primary w-25"><?php echo xlt("Next") ?></a>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        function setupCamera(videoElement) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    videoElement.srcObject = stream;
                })
                .catch(function(error) {
                    console.error("Error accessing camera: ", error);
                });
        }

        function captureImage(videoElement, canvasElement, previewElement, captureBtn, retakeBtn, uploadBtn) {
            let context = canvasElement.getContext("2d");
            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;
            context.drawImage(videoElement, 0, 0);
            let imageData = canvasElement.toDataURL("image/png");
            previewElement.src = imageData;

            videoElement.classList.add("hide");
            previewElement.classList.remove("hide");
            captureBtn.classList.add("hide");
            retakeBtn.classList.remove("hide");
            uploadBtn.classList.remove("hide");
        }

        function retakeImage(videoElement, previewElement, captureBtn, retakeBtn, uploadBtn) {
            videoElement.classList.remove("hide");
            previewElement.classList.add("hide");
            captureBtn.classList.remove("hide");
            retakeBtn.classList.add("hide");
            uploadBtn.classList.add("hide");
        }

        function uploadImage(canvasElement, fileType) {
            let imageData = canvasElement.toDataURL("image/png");
            let formData = new FormData();
            formData.append("image", imageData);
            formData.append("type", fileType);
            formData.append("pid", "<?php echo $_SESSION['pid']; ?>");
            formData.append("csrf_token_form", "<?php echo $_SESSION['csrf_token_form']; ?>");

            $.ajax({
                url: "../public/insuranceCard.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        let jsonResponse = JSON.parse(response);
                        if (jsonResponse.status === "success") {
                            alert("Upload successful: " + jsonResponse.message);
                        } else {
                            alert("Upload failed: " + jsonResponse.message);
                        }
                    } catch (e) {
                        alert("Upload response error: Invalid JSON format.");
                        console.error("Invalid JSON response:", response);
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = `Upload failed. Status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        try {
                            let jsonError = JSON.parse(xhr.responseText);
                            errorMessage += `\nServer Response: ${jsonError.message}`;
                        } catch (e) {
                            errorMessage += `\nRaw Response: ${xhr.responseText}`;
                        }
                    }
                    alert(errorMessage);
                    console.error("Upload Error:", errorMessage);
                }
            });
        }

        // Setup cameras
        setupCamera(document.getElementById("videoFront"));
        setupCamera(document.getElementById("videoBack"));

        // Capture buttons
        $("#captureFront").click(() => captureImage(
            $("#videoFront")[0], $("#canvasFront")[0], $("#previewFront")[0],
            $("#captureFront")[0], $("#retakeFront")[0], $("#uploadFront")[0]
        ));
        $("#captureBack").click(() => captureImage(
            $("#videoBack")[0], $("#canvasBack")[0], $("#previewBack")[0],
            $("#captureBack")[0], $("#retakeBack")[0], $("#uploadBack")[0]
        ));

        // Retake buttons
        $("#retakeFront").click(() => retakeImage(
            $("#videoFront")[0], $("#previewFront")[0], $("#captureFront")[0],
            $("#retakeFront")[0], $("#uploadFront")[0]
        ));
        $("#retakeBack").click(() => retakeImage(
            $("#videoBack")[0], $("#previewBack")[0], $("#captureBack")[0],
            $("#retakeBack")[0], $("#uploadBack")[0]
        ));

        // Upload buttons using the existing insuranceCard.php script
        $("#uploadFront").click(() => uploadImage($("#canvasFront")[0], "front"));
        $("#uploadBack").click(() => uploadImage($("#canvasBack")[0], "back"));
    });
</script>
</body>
</html>
