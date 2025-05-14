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

class VisitIntakeController
{
    public function startVisitIntake(Request $request, Response $response, array $args): Response {
        $data = json_decode($request->getBody()->getContents(), true);

        if (!isset($data['pid']) || !isset($data['user_id'])) {
            $response->getBody()->write(json_encode(["error" => "Patient ID and User ID are required"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $_SESSION['visit_intake'] = [
            "pid" => (int)$data['pid'],
            "user_id" => (int)$data['user_id'],
            "responses" => []
        ];

        $response->getBody()->write(json_encode(["message" => "Visit intake session started"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateVisitIntake(Request $request, Response $response, array $args): Response {
        $data = json_decode($request->getBody()->getContents(), true);

        if (!isset($data['pid']) || !isset($data['user_id']) || !isset($data['field']) || !isset($data['value'])) {
            $response->getBody()->write(json_encode(["error" => "Patient ID, User ID, Field, and Value are required"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $_SESSION['visit_intake']['responses'][$data['field']] = $data['value'];

        $response->getBody()->write(json_encode(["message" => "Response recorded", "responses" => $_SESSION['visit_intake']['responses']]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getVisitIntakeProgress(Request $request, Response $response, array $args): Response {
        if (!isset($_SESSION['visit_intake'])) {
            $response->getBody()->write(json_encode(["error" => "No active visit intake session"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $requiredFields = ["visit_reason", "related_condition"];

        $responses = $_SESSION['visit_intake']['responses'];
        $missingFields = array_diff($requiredFields, array_keys($responses));

        $response->getBody()->write(json_encode([
            "completed" => empty($missingFields),
            "missing" => $missingFields
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function completeVisitIntake(Request $request, Response $response, array $args): Response {
        if (!isset($_SESSION['visit_intake'])) {
            $response->getBody()->write(json_encode(["error" => "No active visit intake session"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $requiredFields = ["visit_reason", "related_condition"];

        $responses = $_SESSION['visit_intake']['responses'];
        $missingFields = array_diff($requiredFields, array_keys($responses));

        if (!empty($missingFields)) {
            $response->getBody()->write(json_encode(["error" => "Missing required fields", "missing" => $missingFields]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $_SESSION['visit_intake']['completed'] = true;

        $response->getBody()->write(json_encode(["message" => "Visit intake completed successfully"]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
