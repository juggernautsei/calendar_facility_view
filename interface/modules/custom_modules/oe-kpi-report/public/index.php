<?php

/*
 * @package OpenEMR
 *
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.
 * @license "All rights reserved"
 */


require_once dirname(__DIR__, 4) . '/globals.php';

use OpenEMR\Common\Twig\TwigContainer;
use Juggernaut\Module\KPI\Services\DashboardServices;


function DateToYYYYMMDD_js($date)
{
    $date = new DateTime($date);
    return $date->format('m-Y');
}

$twig = new TwigContainer(dirname(__DIR__) . '/templates', $GLOBALS["kernel"]);
$t = $twig->getTwig();

$dashboardServices = new DashboardServices();

$lastMonth = $dashboardServices->getFirsDayOfLastMonth();

$chargesAndAdjustments = $dashboardServices->collections();
$insurance = $dashboardServices->insuranceDistribution();
$insurerCharges = $dashboardServices->sortInsuranceDistribution($insurance);

$listInsurances = implode(", ", $insurerCharges[0]);
$paymentsByInsurance = implode(", ", $insurerCharges[1]);
$totalCollectionsByPayer = array_sum($insurerCharges[1]);
$viewArgs = [
    'previousMonth' => DateToYYYYMMDD_js($lastMonth),
    'newPatientsSeen' => $dashboardServices->getNewPatientsSeen(),
    'appointmentsScheduled' => $dashboardServices->getTotalScheduledAppointments(),
    'visitsCompleted' => $dashboardServices->getVisitsCompleted(),
    'documentedVisits' => $dashboardServices->getDocumentedVisits(),
    'billedVisits' => $dashboardServices->getBilledVisits(),
    'activePatients' => rand(1999, 19999),
    'noShow' => $dashboardServices->getNoShow(),
    'cancelled' => $dashboardServices->getCancelled(),
    'rescheduled' => $dashboardServices->getRescheduled(),
    'cancelledByProvider' => $dashboardServices->getCancelledByProvider(),
    'chargesMonth' => $chargesAndAdjustments[0],
    'adjustmentsMonth' => $chargesAndAdjustments[1],
    'payers' => $listInsurances,
    'insurersPaid' => $paymentsByInsurance,
    'payerSum' => $totalCollectionsByPayer,
];

echo $t->render("/dashboard.twig", $viewArgs);
