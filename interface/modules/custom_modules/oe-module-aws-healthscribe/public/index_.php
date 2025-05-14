<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";
require_once dirname(__DIR__, 4) . "/globals.php";

use OpenEMR\Core\Header;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AWS Transcribe Medical Stream</title>
    <?php Header::setupHeader(); ?>
    <script>
        const WORKLET_PROCESSOR_PATH = "<?php echo $GLOBALS['web_root']; ?>/interface/modules/custom_modules/oe-module-aws-healthscribe/public/worklet-processor.js";

        let websocket;
        let mediaStream;
        let audioContext;
        let audioInput;
        let workletNode;

        async function startStream(presignedUrl) {
            if (!presignedUrl) {
                alert('Presigned URL is missing. Please check the server-side implementation.');
                return;
            }

            try {
                // Request microphone access
                mediaStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                console.log('Microphone access granted.');

                // Setup WebSocket
                websocket = new WebSocket(presignedUrl);

                websocket.onopen = async () => {
                    console.log('WebSocket connection established.');

                    // Setup Audio Context
                    audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    audioInput = audioContext.createMediaStreamSource(mediaStream);

                    // Add AudioWorkletProcessor
                    try {
                        await audioContext.audioWorklet.addModule(WORKLET_PROCESSOR_PATH);
                    } catch (error) {
                        console.error('Error adding AudioWorkletProcessor:', error);
                        alert('Error adding AudioWorkletProcessor. Please check the server-side implementation.');
                        return;
                    }
                    workletNode = new AudioWorkletNode(audioContext, 'audio-processor');

                    workletNode.port.onmessage = (event) => {
                        if (websocket && websocket.readyState === WebSocket.OPEN) {
                            websocket.send(event.data);
                        }
                    };

                    audioInput.connect(workletNode).connect(audioContext.destination);
                };

                websocket.onmessage = (event) => {
                    console.log('Transcription result:', event.data);

                    const blob = event.data;

                    // Check if it's a Blob object
                    if (blob instanceof Blob) {
                        // Create a FileReader to read the Blob content
                        const reader = new FileReader();

                        reader.onloadend = function () {
                            const text = reader.result;
                            console.log('Transcription blob:', blob);
                            console.log('Transcription result:', text);
                            // Display the transcription result
                            const output = document.getElementById('output');
                            const newLine = document.createElement('p');
                            newLine.textContent = text;  // Add the transcribed text
                            output.appendChild(newLine);
                        };

                        // Read the Blob as text
                        reader.readAsText(blob);
                    } else {
                        // If it's not a Blob, directly handle the data
                        console.log('Unexpected data format:', event.data);
                    }
                };

                websocket.onerror = (error) => {
                    console.error('WebSocket error:', error);
                };

                websocket.onclose = () => {
                    console.log('WebSocket connection closed.');
                    cleanupAudio();
                };
            } catch (error) {
                console.error('Error accessing microphone:', error);
            }
        }

        function stopStream() {
            if (websocket) {
                websocket.close();
                console.log('WebSocket connection closed by user.');
            }
            cleanupAudio();
        }

        function cleanupAudio() {
            if (audioContext && audioContext.state !== 'closed') {
                audioContext.close();
            }
            if (mediaStream) {
                mediaStream.getTracks().forEach((track) => track.stop());
            }
        }
    </script>
</head>
<body>
<div class="container">
    <div class="row mt-5">
        <div class="col-md-12 text-center">
            <h1><?php echo xlt('AWS Transcribe Medical Stream'); ?></h1>
        </div>
        <div class="col-md-12 mt-5 text-center">
            <button class="btn btn-success mr-3" onclick="startStream('<?php echo generatePresignedUrl(); ?>')">Start Stream</button>
            <button class="btn btn-danger ml-3" onclick="stopStream()">Stop Stream</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2>Transcription Output:</h2>
            <pre id="output" style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd;"></pre>
        </div>
    </div>
</div>
<?php

use Aws\Credentials\Credentials;
use Aws\Signature\SignatureV4;
use GuzzleHttp\Psr7\Request;

use Juggernaut\Module\HealthScribe\Controllers\AwsCredentialManager;
use OpenEMR\Common\Crypto\CryptoGen;

function generatePresignedUrl()
{
    $awsCredentialManager = new AwsCredentialManager();
    $credentialEntered = $awsCredentialManager->areCredentialsSaved();
    $crypto = new CryptoGen();
    $accessKey = $crypto->decryptStandard($credentialEntered['access_key']);
    $secretKey = $crypto->decryptStandard($credentialEntered['secret_key']);
    $region = $credentialEntered['region'];
    $service = 'transcribe';

    // Credentials setup
    $credentials = new Credentials($accessKey, $secretKey);
    $signer = new SignatureV4($service, $region);

    // WebSocket endpoint and query parameters
    $url = "wss://transcribestreaming.$region.amazonaws.com:8443/medical-stream-transcription-websocket";
    $params = [
        'language-code' => 'en-US',
        'media-encoding' => 'pcm',
        'sample-rate' => '16000',
        'session-id' => uniqid('session_', true),
        'specialty' => 'PRIMARYCARE',
        'type' => 'DICTATION',
    ];

    // Generate query string
    $queryString = http_build_query($params);

    // Create the request
    $request = new Request('GET', $url . '?' . $queryString);

    // Sign the request
    $signedRequest = $signer->signRequest($request, $credentials);

    // Return presigned URL
    return (string) $signedRequest->getUri();
}
?>
</body>
</html>
