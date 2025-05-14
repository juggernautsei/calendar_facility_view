<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

namespace Juggernaut\Module\HospitalCharge\Controllers;

class SearchController
{
    public function findPatient($term)
    {
        // Search for patient
        // Return JSON response
        return PatientSearch::search($term);
    }
    public function findProvider($term)
    {
        // Search for provider
        // Return JSON response
        return ProviderSearch::search($term);
    }
    public function findCode($type, $term)
    {
        // Search for code
        // Return JSON response
        return CodeSearch::search($type, $term);
    }

}
