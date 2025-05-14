<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

namespace Juggernaut\Module\WoundCare\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class WoundAssessmentController
{
    public function startAssessment(Request $request, Response $response, array $args): Response {
        $data = json_decode($request->getBody()->getContents(), true);

        if (!isset($data['pid']) || !isset($data['user_id'])) {
            $response->getBody()->write(json_encode(["error" => "Patient ID and User ID are required"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $pid = (int)$data['pid'];
        $user_id = (int)$data['user_id'];

        $_SESSION['wound_assessment'] = [
            "pid" => $pid,
            "user_id" => $user_id,
            "responses" => []
        ];

        $response->getBody()->write(json_encode(["message" => "Wound assessment session started", "pid" => $pid, "user_id" => $user_id]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateAssessment(Request $request, Response $response, array $args): Response {
        $data = json_decode($request->getBody()->getContents(), true);

        if (!isset($data['pid']) || !isset($data['user_id']) || !isset($data['field']) || !isset($data['value'])) {
            $response->getBody()->write(json_encode(["error" => "Patient ID, User ID, Field, and Value are required"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $_SESSION['wound_assessment']['responses'][$data['field']] = $data['value'];

        $response->getBody()->write(json_encode(["message" => "Response recorded", "responses" => $_SESSION['wound_assessment']['responses']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getAssessmentProgress(Request $request, Response $response, array $args): Response {
        if (!isset($_SESSION['wound_assessment'])) {
            $response->getBody()->write(json_encode(["error" => "No active wound assessment session"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $requiredFields = [
            "wound_location", "wound_size", "wound_type", "exudate", "wound_edges", "wound_bed",
            "drainage_type", "odor", "periwound_condition", "treatment", "dressing"
        ];

        $responses = $_SESSION['wound_assessment']['responses'];
        $missingFields = array_diff($requiredFields, array_keys($responses));

        $response->getBody()->write(json_encode([
            "completed" => empty($missingFields),
            "missing" => $missingFields
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function completeAssessment(Request $request, Response $response, array $args): Response {
        if (!isset($_SESSION['wound_assessment'])) {
            $response->getBody()->write(json_encode(["error" => "No active wound assessment session"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $requiredFields = [
            "wound_location", "wound_size", "wound_type", "exudate", "wound_edges", "wound_bed",
            "drainage_type", "odor", "periwound_condition", "treatment", "dressing"
        ];

        $responses = $_SESSION['wound_assessment']['responses'];
        $missingFields = array_diff($requiredFields, array_keys($responses));

        if (!empty($missingFields)) {
            $response->getBody()->write(json_encode(["error" => "Missing required fields", "missing" => $missingFields]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $sql = "INSERT INTO wound_assessments (pid, user_id, wound_location, wound_size, wound_type, exudate, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        sqlStatement($sql, [
            $_SESSION['wound_assessment']['pid'],
            $_SESSION['wound_assessment']['user_id'],
            $responses["wound_location"],
            $responses["wound_size"],
            $responses["wound_type"],
            $responses["exudate"]
        ]);

        unset($_SESSION['wound_assessment']); // Clear session after storing

        $response->getBody()->write(json_encode(["message" => "Wound assessment completed and stored successfully"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
