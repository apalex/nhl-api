<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller for handling Shot on Goal Percentage (SOGP) computation requests.
 */
class SOGPController extends BaseController
{
    /**
     * Constructor for SOGPController.
     */
    public function __construct() {}

    /**
     * Handles POST requests to compute the Shot on Goal Percentage (SOGP).
     *
     * This endpoint expects a JSON body with the following fields:
     * - goals_scored (int)
     * - shot_on_target (int)
     *
     * @param Request $request The HTTP request containing player stats in the body.
     * @param Response $response The HTTP response to return the SOGP value or an error.
     * @return Response JSON response with either a calculated SOGP or an error message.
     */
    public function handlePostSOGP(Request $request, Response $response): Response
    {
        //? Step 1 - parse body using getParsedBody
        $body = (array) $request->getParsedBody();

        //? Step 2 - list required fields and validate
        $requiredFields = ['goals_scored', 'shot_on_target'];
        foreach ($requiredFields as $requiredField) {
            if (!isset($body[$requiredField]) || !is_numeric($body[$requiredField])) {
                return $this->renderJson($response, ["Error" => "Missing or invalid field: $requiredField"], 400);
            }
        }

        $goals_scored = (float) $body['goals_scored'];
        $shot_on_target = (float) $body['shot_on_target'];

        //? Step 3 - error handling

        if ($goals_scored < 0 || $shot_on_target < 0) {
            return $this->renderJson($response, ["Error" => "Negative values are not allowed!"], 400);
        }

        //? Step 4 - Calculate SOGP
        $sogp = ($goals_scored / $shot_on_target) * 100;

        //? Step 5 - Return
        return $this->renderJson($response,
        [
            "status" => array(
                "Type" => "successful",
                "Code" => 200,
                "Content-Type" => "application/json"
            ),
            "Shot on Goal %" => round($sogp, 3)
        ]);
    }
}
