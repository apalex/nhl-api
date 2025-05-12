<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles possession percentage calculation based on user-provided shot data.
 *
 * Endpoint: POST /possession
 *
 * Requirements:
 * - Requires JWT Authorization with admin role.
 * - Accepts JSON body with `shotsFor` and `shotsAgainst` values.
 *
 * Example Request Body:
 * {
 *    "shotsFor": 35,
 *    "shotsAgainst": 25
 * }
 */
class PossessionController extends BaseController
{
    /**
     * Calculates possession percentage based on shots for and against.
     *
     * @param Request $request The HTTP request containing JSON body.
     * @param Response $response The HTTP response object to return results.
     *
     * @return Response The HTTP response containing possession calculation results.
     */
    public function calculatePossession(Request $request, Response $response): Response
    {
        $data = (array) $request->getParsedBody();

        // Validate Input
        if (!isset($data['shotsFor'], $data['shotsAgainst'])) {
            $response->getBody()->write(json_encode([
                'error' => 'Missing shotsFor or shotsAgainst in JSON body'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $shotsFor = (int) $data['shotsFor'];
        $shotsAgainst = (int) $data['shotsAgainst'];
        $totalShots = $shotsFor + $shotsAgainst;

        $possession = ($totalShots === 0) ? 0 : round(($shotsFor / $totalShots) * 100, 2);

        // Success Response
        //? Step 5 - Return
        return $this->renderJson($response,
        [
            "status" => array(
                "Type" => "successful",
                "Code" => 200,
                "Content-Type" => "application/json"
            ),
            'shotsFor' => $shotsFor,
            'shotsAgainst' => $shotsAgainst,
            'possessionPercentage' => $possession . '%'
        ]);
    }
}
