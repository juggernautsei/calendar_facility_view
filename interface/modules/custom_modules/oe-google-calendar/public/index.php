<?php
//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);

global $oauth2_client_id, $oauth2_redirect, $oauth2_secret;

require_once dirname(__DIR__, 4) . '/globals.php';
require_once('config.php');


use OpenEMR\Core\Header;
use OpenEMR\Common\Csrf\CsrfUtils;
use Juggernaut\OpenEMR\Modules\GoogleCalendar\Services\TokenServices;
use Juggernaut\OpenEMR\Modules\GoogleCalendar\Services\CredentialService;

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $prefix = "https";
} else {
    $prefix = "http";
}
$redirect_uri = $prefix . "://" . $_SERVER['HTTP_HOST'] . "/interface/modules/custom_modules/oe-google-calendar/public/oauth2callback";
$access_token = new TokenServices();
$token_service = $access_token->getToken();
$credential = new CredentialService();
$credentials = $credential->getCredentials();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <?php Header::setupHeader(); ?>
    <?php
    $csrfToken = $_SESSION['site_id'] . "-" . CsrfUtils::collectCsrfToken();
    $link = "https://accounts.google.com/o/oauth2/auth?scope=https://www.googleapis.com/auth/cloud-platform&response_type=code&iclude_granted_scopes=true&access_type=offline&state=$csrfToken&redirect_uri=$redirect_uri&client_id=$oauth2_client_id";
    ?>
</head>
<body>
<div class="container mt-4">
    <h1><?php echo xlt('Google Calendar') ?></h1>
    <div class="row">
        <div class="col-md-12">
            <h3><?php echo "Instructions for creating Google API" ?></h3>
            <ol>
                <li><?php echo xlt("Go to ") ?><a href="https://console.developers.google.com/" target="_blank">Google Developer Console</a></li>
                <li><?php echo xlt("Create a new project") ?></li>
                <li><?php echo xlt("Enable the Google Calendar API") ?></li>
                <li><?php echo xlt("Create OAuth2 credentials") ?></li>
                <li><?php echo xlt("Set the redirect URI to") ?> <br> <strong>https://<?php echo $_SERVER['HTTP_HOST'] ?>/interface/modules/custom_modules/oe-google-calendar/public/oauth2callback</strong></li>
                <li><?php echo xlt("Copy the client ID and client secret into the form below") ?></li>
            </ol>
        </div>
    </div>
    <div id="credentialsave" class="row mb-1">
        <div class="col-md-6">
            <h3><?php echo xlt('Saving Auth Credentials') ?></h3>
            <form id="myForm" action="../oe-google-calendar/public/save.php" method="post">
                <input type="hidden" name="csrf_token_form" value="<?php $token = explode('-', $csrfToken); echo $token[1] ?>">
                <div class="form-group">
                    <label for="client_id"><?php echo xlt('Client ID') ?></label>
                    <input type="text" class="form-control" id="client_id" name="client_id" value="">
                </div>
                <div class="form-group">
                    <label for="client_secret"><?php echo xlt('Client Secret') ?></label>
                    <input type="text" class="form-control" id="client_secret" name="client_secret" value="">
                </div>
                <div class="form-group">
                    <label for="redirect_uri"><?php echo xlt('Redirect URI') ?></label>
                    <input type="text" class="form-control" id="redirect_uri" name="redirect_uri" value="">
                </div>
                <button type="submit" class="btn btn-primary" ><?php echo xlt('Save') ?></button>
            </form>
        </div>
    </div>
    <div id="authorizeapp" class="row mt-4">
        <div class="col-md-6">
            <p><?php echo xlt('Now authorize this app to use your Google account. This will work on only one account.'); ?></p>
            <p><?php echo xlt('Use the account that the calendar is attached.'); ?></p>
            <a class="btn btn-primary" href="<?php echo $link; ?>" target="_blank"><?php echo xlt('Authorize Calendar Access') ?></a>
        </div>
    </div>
    <div id="basecomplete" class="row mt-4">
        <div class="col-md-6">
            <h4>**<?php echo xlt('You have successfully authorized the app to access your Google Calendar.'); ?></h4>
            <p><?php echo xlt('To reset: uninstall module to clear credentials to use a different calendar'); ?></p>
            <a class="btn btn-primary" href="public/calendar_list.php"><?php echo xlt('View Calendar List') ?></a>
        </div>
    </div>

</div>
<script>
    //hide the authorize button if the token is already saved
    var token
    token = "<?php echo $token_service['access_token']; ?>";
    //hide the save credentials form if the credentials are already saved
    var credentials
    credentials = "<?php echo $credentials['client_id']; ?>";

    document.getElementById('basecomplete').style.display = 'none';

    if (!token) {
        document.getElementById('authorizeapp').style.display = 'none';
    }
    if (token) {
        document.getElementById('basecomplete').style.display = 'block';
        //document.getElementById('authorizeapp').style.display = 'none';
    }
    if (credentials) {
        document.getElementById('credentialsave').style.display = 'none';
    }

</script>
<script src="js/form-validation.js"></script>
</body>
</html>


