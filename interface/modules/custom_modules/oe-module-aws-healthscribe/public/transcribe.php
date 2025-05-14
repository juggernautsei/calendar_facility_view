<?php


require __DIR__ . '/../vendor/autoload.php';
require_once dirname(__DIR__, 4) . "/globals.php";

use OpenEMR\Core\Header;
use Juggernaut\Module\HealthScribe\Controllers\AwsCredentialManager;
use OpenEMR\Common\Crypto\CryptoGen;
use Aws\Signature\SignatureV4;
use Aws\Credentials\Credentials;
use GuzzleHttp\Psr7\Request;

$awsCredentialManager = new AwsCredentialManager();
$credentialEntered = $awsCredentialManager->areCredentialsSaved();
$crypto = new CryptoGen();
$accessKey = $crypto->decryptStandard($credentialEntered['access_key']);
$secretKey = $crypto->decryptStandard($credentialEntered['secret_key']);
$region = $credentialEntered['region'];
$sessionToken = null;

// Function to generate presigned URL
function generatePresignedUrl($region, $accessKey, $secretKey, $sessionToken = null)
{
    $endpoint = "wss://transcribestreaming.$region.amazonaws.com:8443/medical-stream-transcription-websocket";
    $queryParams = [
        'language-code' => 'en-US',
        'media-encoding' => 'pcm',
        'sample-rate' => 16000,
        'specialty' => 'PRIMARYCARE',
        'type' => 'DICTION',
    ];

    $queryString = http_build_query($queryParams);
    $url = "$endpoint?$queryString";

    $credentials = new Credentials($accessKey, $secretKey, $sessionToken);
    $signature = new SignatureV4('transcribe', $region);

    $request = new Request('GET', $url);
    $signedRequest = $signature->presign($request, $credentials, '+5 minutes');

    return (string)$signedRequest->getUri();
}

try {
    $presignedUrl = generatePresignedUrl($region, $accessKey, $secretKey, $sessionToken);
    //echo json_encode(['status' => 'success', 'url' => $presignedUrl]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AWS Transcribe Medical Stream</title>
    <?php Header::setupHeader(); ?>
</head>
<body>
<div class="container">
    <div class="row mt-5">
        <div class="col-md-12 text-center">
            <h1>Real-Time Medical Transcription</h1>
            <button class="btn btn-success mr-3" onclick="startStream()">Start Stream</button>
            <button class="btn btn-danger ml-3" onclick="stopStream()">Stop Stream</button>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-12">
            <h2>Transcription Output:</h2>
            <pre id="output" style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd;"></pre>
        </div>
    </div>
</div>
<script>
    let websocket = null;
    let mediaRecorder = null;

    async function startStream() {
        try {

            const wsUrl = '<?php echo $presignedUrl; ?>';
            websocket = new WebSocket(wsUrl);

            websocket.onopen = () => {
                console.log('WebSocket connection established.');
                startAudioStream();
            };

            websocket.onmessage = async event => {
                try {
                    // Convert Blob to text
                    const text = await event.data.text();

                    // Parse the text as JSON
                    const result = JSON.parse(text);

                    // Process the transcription results
                    if (result.Transcript && result.Transcript.Results) {
                        const output = document.getElementById('output');
                        result.Transcript.Results.forEach(item => {
                            if (!item.IsPartial) {
                                output.textContent += item.Alternatives[0].Transcript + '\n';
                            }
                        });
                    }
                } catch (error) {
                    console.error('Error processing WebSocket message:', error);
                }
            };

            websocket.onerror = error => {
                console.error('WebSocket error:', error);
            };

            websocket.onclose = () => {
                console.log('WebSocket connection closed.');
                stopAudioStream();
            };
        } catch (error) {
            console.error('Error starting stream:', error);
            alert('Failed to start transcription stream. See console for details.');
        }
    }

    async function startAudioStream() {
        const audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(audioStream, { mimeType: 'audio/webm;codecs=opus' });

        mediaRecorder.ondataavailable = event => {
            if (websocket && websocket.readyState === WebSocket.OPEN) {
                websocket.send(event.data);
            }
        };

        mediaRecorder.start(250); // Send audio chunks every 250ms
        console.log('Audio stream started.');
    }

    function stopStream() {
        if (mediaRecorder) {
            mediaRecorder.stop();
        }
        if (websocket) {
            websocket.close();
        }
        console.log('Stream stopped.');
    }
</script>
</body>
</html>
