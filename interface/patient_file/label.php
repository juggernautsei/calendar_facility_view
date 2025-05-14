<?php
global $pid;

/**
 * interface/patient_file/label.php Displaying a PDF file of Labels for printing.
 *
 * Program for displaying Chart Labels
 * via the popups on the left nav screen
 *
 * Used the program example supplied with the Avery Label Print Class to produce this program
 *
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Terry Hill <terry@lillysystems.com>
 * @copyright Copyright (c) 2014 Terry Hill <terry@lillysystems.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once("../globals.php");

if (!$_SESSION['encounter']) {
    die(xlt('No encounter selected! Select an encounter before printing labels.'));
}
//Get the data to place on labels
//
$patdata = sqlQuery("SELECT " .
  "p.fname, p.mname, p.lname, p.pubpid, p.DOB, " .
  "p.street, p.city, p.state, p.postal_code, p.pid " .
  "FROM patient_data AS p " .
  "WHERE p.pid = ? LIMIT 1", array($pid));

// re-order the dates
//
$providerData = sqlQuery(
    "SELECT fe.provider_id, CONCAT(u.lname, ' ', u.fname) AS provider_name, fe.date AS encounter_date
FROM form_encounter fe
JOIN users u ON u.id = fe.provider_id
WHERE fe.encounter = ?", array($_SESSION['encounter'])
);

$dateofservice = oeFormatShortDate($providerData['encounter_date']);
$dob = oeFormatShortDate($patdata['DOB']);

//get label type and number of labels on sheet
//

if ($GLOBALS['chart_label_type'] == '1') {
    $pdf = new PDF_Label('5160');
    $last = 30;
}

if ($GLOBALS['chart_label_type'] == '2') {
    $pdf = new PDF_Label('5161');
    $last = 20;
}

if ($GLOBALS['chart_label_type'] == '3') {
    $pdf = new PDF_Label('5162');
    $last = 14;
}

$pdf->AddPage();

// Added spaces to the sprintf for Fire Fox it was having a problem with alignment
//$text = sprintf("  %s %s\n  %s\n  %s\n  %s", $patdata['fname'], $patdata['lname'], $dob, $today, $patdata['pid']);
$text = sprintf("  %s %s\n  %s\n  %s MRN: %s\n  %s", $patdata['fname'], $patdata['lname'], $dob, $dateofservice, $patdata['pid'], $providerData['provider_name']);
// For loop for printing the labels
//

for ($i = 1; $i <= $last; $i++) {
    $pdf->Add_Label($text);
}

$pdf->Output();
