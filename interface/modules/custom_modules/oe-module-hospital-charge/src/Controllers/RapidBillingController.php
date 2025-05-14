<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

namespace Juggernaut\Module\HospitalCharge\Controllers;

use OpenEMR\Billing\BillingUtilities;
use Juggernaut\Module\HospitalCharge\App\Model\HomeModel;
use OpenEMR\Common\Twig\TwigContainer;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class RapidBillingController
{
    private array $claim;
    private BillingUtilities $claimProcessor;
    private HomeModel $data;
    private \Twig\Environment $template;
    private TwigContainer $twig;

    public function __construct()
    {
        $this->claim = [];
        $this->claimProcessor = new BillingUtilities();
        $this->data = new HomeModel();
        $this->twig = new TwigContainer(dirname(__FILE__, 3) . '/templates', $GLOBALS["kernel"]);
        $this->template = $this->twig->getTwig();
    }

    public function rapidClaim()
    {
        //TODO fix this so that it returns to the form with error
        $this->claim = $_POST;
        if (empty($this->claim['facility'])) {
            return json_encode(['error' => 'No data provided']);
        }

        try {
            // Create an encounter
            $encounterId = $this->create_encounter(
                $this->claim['selected_patient_id'],
                $this->claim['selected_provider_id'],
                $this->claim['servicedate'],
            );

            if (!$encounterId) {
                throw new \Exception('Encounter creation failed');
            }

            // Store billing data
            foreach ($this->claim['rows'] as $billingItem) {
                $ndc_info = '';
                if (!empty($billingItem['ndcnum'])) {
                    $ndc_info = 'N4' . trim($billingItem['ndcnum']) . '   ' . $billingItem['ndcuom'] .
                        trim($billingItem['ndcqty']);
                }
                $this->claimProcessor::addBilling(
                    $encounterId,
                    $billingItem['codetype'],
                    $billingItem['code'],
                    $billingItem['code_text'],
                    $this->claim['selected_patient_id'],
                    null,
                    0,
                    $billingItem['modifier'],
                    $billingItem['units'],
                    $billingItem['fee'],
                    $ndc_info,
                    $billingItem['justify'],
                );
            }
            $populate = $this->data->getHomeData();
            $populate['rapid_data'] = [
                'status' => 'success',
                'message' => 'Claim created successfully',
                'encounter' => $encounterId,
            ];
            return $this->template->render('home/home.twig', $populate);
        } catch (\Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    private function create_encounter($pid, $provider_id, $order_date): int
    {
        require_once dirname(__DIR__, 6) . "/library/forms.inc.php";
        $conn = $GLOBALS['adodb']['db'];
        $encounter = $conn->GenID("sequences");
        $facility_id = $this->claim['facility'];
        addForm(
            $encounter,
            "Auto Generated Hospital Encounter",
            sqlInsert(
                "INSERT INTO form_encounter SET " .
                "date = ?, " .
                "onset_date = '', " .
                "reason = ?, " .
                "sensitivity = 'normal', " .
                "referral_source = '', " .
                "pid = ?, " .
                "encounter = ?, " .
                "provider_id = ?," .
                "facility_id = ?," .
                "pc_catid = 9," .
                "pos_code = 11",

                array(
                    date('Y-m-d H:i:s', strtotime($order_date)),
                    "Generated encounter for hospital visit",
                    $pid,
                    $encounter,
                    ($provider_id ?? ''),
                    ($facility_id ?? '')
                )
            ),
            "newpatient",
            $pid,
            0,
            date('Y-m-d'),
            'SYSTEM'
        );
        return $encounter ?: 0;
    }
}

