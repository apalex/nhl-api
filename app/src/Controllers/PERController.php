<?php

namespace App\Controllers;

use App\Models\PERModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller for handling Player Efficiency Rating (PER) computation requests.
 */
class PERController extends BaseController
{
    /**
     * Constructor for PERController.
     *
     * @param PERModel $perModel An instance of PERModel used to perform the PER calculation.
     */
    public function __construct(private PERModel $perModel) {}

    /**
     * Handles POST requests to compute the Player Efficiency Rating (PER).
     *
     * This endpoint expects a JSON body with the following fields:
     * - goals (float or int)
     * - assists (float or int)
     * - plus_minus (float or int)
     * - penalty_minutes (float or int)
     * - games_played (float or int)
     *
     * @param Request $request The HTTP request containing player stats in the body.
     * @param Response $response The HTTP response to return the PER value or an error.
     * @return Response JSON response with either a calculated PER or an error message.
     */
    public function handlePostPER(Request $request, Response $response): Response
    {
        //? Step 1 - parse body using getParsedBody
        $body = (array) $request->getParsedBody();

        //? Step 2 - list required fields and validate
        $requiredFields = ['goals', 'assists', 'plus_minus', 'penalty_minutes', 'games_played'];
        foreach ($requiredFields as $requiredField) {
            if (!isset($body[$requiredField]) || !is_numeric($body[$requiredField])) {
                return $this->renderJson($response, ["Error" => "Missing or invalid field: $requiredField"], 400);
            }
        }

        $goals = (float) $body['goals'];
        $assists = (float) $body['assists'];
        $plusMinus  = (float) $body['plus_minus'];
        $penaltyMinutes  = (float) $body['penalty_minutes'];
        $gamesPlayed  = (float) $body['games_played'];

        //? Step 3 - error handling

        if ($gamesPlayed == 0) {
            return $this->renderJson($response, ["Error" => "Games played cannot be zero!"], 400);
        }

        //? Step 4 - Call calculate function from model and input required fields
        $per = $this->perModel->calculatePER($goals, $assists, $plusMinus, $penaltyMinutes, $gamesPlayed);

        //? Step 5 - Return
        return $this->renderJson($response, ["player_efficiency_rating" => round($per, 3)]);
    }
}
