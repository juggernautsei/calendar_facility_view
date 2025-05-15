<?php
// facilityView.php

// Adjust the path as necessary to correctly point to globals.php
require_once(__DIR__ . '/../../globals.php'); // Or a more robust way to get to the root

use OpenEMR\Common\Twig\TwigContainer;
// Assuming you create a service class for your logic:
// use OpenEMR\Services\Calendar\FacilityViewService; // Or your chosen namespace and class name

// --- 1. Security and Permissions Check ---
// Example: Ensure user is logged in and has calendar access rights
// This is critical and needs to be implemented according to OpenEMR's security practices.
// if (!ACL::check('facility_calendar_view_permission')) { // Fictional permission
//     http_response_code(403);
//     echo "Access Denied.";
//     exit;
// }

// --- 2. Get Request Parameters ---
$facilityId = isset($_GET['facility_id']) ? $_GET['facility_id'] : null; // Or from $_POST, with proper sanitization/validation
$date = $_GET['date'] ?? date('Y-m-d');   // Default to today, with proper validation

// Validate parameters (e.g., facilityId should be an int, date should be valid)
if (empty($facilityId) /* || other validation checks */) {
    http_response_code(400);
    echo "Missing or invalid parameters.";
    exit;
}

// --- 3. Delegate to a Service Class to Get Data ---
// $facilityViewService = new FacilityViewService($GLOBALS['dbh']); // Pass dependencies if needed
// $appointments = $facilityViewService->getAppointmentsForFacility($facilityId, $date);
// For now, let's assume placeholder data until the service class is built:
$appointments = [
    // Example structure:
    // ['time' => '09:00 AM', 'patientName' => 'John Doe', 'providerName' => 'Dr. Smith', 'notes' => 'Checkup'],
    // ['time' => '09:30 AM', 'patientName' => 'Jane Roe', 'providerName' => 'Dr. Jones', 'notes' => 'Follow-up'],
];
// In a real scenario, you'd fetch this from the database via your service class.
// If the service needs to fetch data from PostCalendar functions, it can call them.

// --- 4. Render with Twig ---
try {
    // The path to your Twig templates directory (relative to where TwigContainer expects or absolute)
    // If your templates are in openemr/templates/, and TwigContainer defaults there, path might be null or 'calendar/'
    $templatePath = null; // Let TwigContainer use its default base path (often $GLOBALS['fileroot']/templates)
    $twigContainer = new TwigContainer($templatePath, isset($GLOBALS['kernel']) ? $GLOBALS['kernel'] : null); // Pass kernel if available for debugging
    $twig = $twigContainer->getTwig();

    $viewVars = [
        'facility_id' => $facilityId,
        'selected_date' => $date,
        'appointments' => $appointments,
        // Add any other variables your Twig template will need
    ];

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
