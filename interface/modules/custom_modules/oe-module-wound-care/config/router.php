<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

global $app;

use Juggernaut\Module\WoundCare\Controllers\WoundAssessmentController;
use Juggernaut\Module\WoundCare\Controllers\TreatmentPlanController;
use Juggernaut\Module\WoundCare\Controllers\Notification\Notification;
use Juggernaut\Module\WoundCare\Controllers\VistIntakeController;


// ➤ Individual Route: Works as expected
$app->get("/notifications", [Notification::class, 'index']);

// ✅ Ensure that all routes work correctly
$app->get('/test', function ($request, $response, $args) {
    $response->getBody()->write(json_encode(["message" => "Slim is working!"]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Serve React frontend from the assistant folder
$app->get('/assistant[/{params:.*}]', function (Request $request, Response $response, array $args) {
    $filePath = __DIR__ . '/../assistant/' . ($args['params'] ?? 'index.html');

    if (!file_exists($filePath)) {
        $filePath = __DIR__ . '/../assistant/index.html'; // Serve main React file for React Router
    }

    $response->getBody()->write(file_get_contents($filePath));
    return $response->withHeader('Content-Type', 'text/html');
});

// ➤ Group Route Fix: Ensure paths start with "/"
$app->group("/wound-care", function ($group) {
    $group->get('', function ($request, $response) {
        $response->getBody()->write(json_encode(["message" => "Wound Care API Root"]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->get('/assessments', [WoundAssessmentController::class, 'getAllAssessments']);
    $group->get('/assessments/{id}', [WoundAssessmentController::class, 'getAssessmentById']);
    $group->post('/assessments', [WoundAssessmentController::class, 'createAssessment']);
    $group->put('/assessments/{id}', [WoundAssessmentController::class, 'updateAssessment']);
    $group->delete('/assessments/{id}', [WoundAssessmentController::class, 'deleteAssessment']);

    $group->get('/treatment-plans/{id}', [TreatmentPlanController::class, 'getTreatmentPlan']);
    $group->post('/treatment-plans', [TreatmentPlanController::class, 'createTreatmentPlan']);
    $group->put('/treatment-plans/{id}', [TreatmentPlanController::class, 'updateTreatmentPlan']);
});

    // Start a new wound assessment session
$app->group('/wound-assessment', function ($group) {
    $group->post('/start', [WoundAssessmentController::class, 'startAssessmentSession']);
    $group->post('/update', [WoundAssessmentController::class, 'updateAssessmentSession']);
    $group->get('/progress', [WoundAssessmentController::class, 'checkAssessmentProgress']);
    $group->post('/complete', [WoundAssessmentController::class, 'completeAssessmentSession']);
});

//UI for the wound care assistant
$app->group('/callAssistant', function ($group) {
    $group->get('', function ($request, $response) {
        $response->getBody()->write(json_encode(["message" => "Wound Care AI Assistant"]));
        return $response->withHeader('Content-Type', 'application/json');
    });
});
