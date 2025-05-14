<?php

Header("X-Frame-Options: DENY");
Header("Content-Security-Policy: frame-ancestors 'none'");

$ignoreAuth = true;

// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once dirname(__DIR__, 3)."/globals.php";

require_once dirname(__DIR__, 4) . "/src/Common/Session/SessionUtil.php";
OpenEMR\Common\Session\SessionUtil::portalSessionStart();

header('Location: public/index.php');
