<?php


// Adjust the path as necessary to correctly point to globals.php
require_once(__DIR__ . '/../../globals.php'); // Or a more robust way to get to the root

use OpenEMR\Common\Acl\AclMain;
use OpenEMR\Common\Twig\TwigContainer;

try {
    // The path to your Twig templates directory (relative to where TwigContainer expects or absolute)
    // If your templates are in openemr/templates/, and TwigContainer defaults there, path might be null or 'calendar/'
    $templatePath = null; // Let TwigContainer use its default base path (often $GLOBALS['fileroot']/templates)
    $twigContainer = new TwigContainer($templatePath, $GLOBALS['kernel']); // Pass kernel if available for debugging
    $twig = $twigContainer->getTwig();

    $thisauth = AclMain::aclCheckCore('patients', 'demo');

    if (!$thisauth) {
        echo $twig->render('core/unauthorized-partial.html.twig', ['pageTitle' => xl("Calendar Facility View")]);
        exit();
    }


    $viewVars = [
//        'facility_id' => $facilityId,
//        'selected_date' => $date,
//        'appointments' => $appointments,
        // Add any other variables your Twig template will need
    ];
    //die("Under construction");
    // Assuming your Twig template is named 'facility_day_view.html.twig'
    // and is located in a 'calendar' subdirectory of the main Twig templates folder
    echo $twig->render('calendar/facilityView/facilityView.html.twig', $viewVars);

} catch (\Exception $e) {
    // Log the error
    error_log("Error rendering facility view: " . $e->getMessage());
    http_response_code(500);
    echo "An error occurred while generating the view."; // User-friendly message
}

exit;
