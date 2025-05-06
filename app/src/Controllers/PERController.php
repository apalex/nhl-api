<?php

namespace App\Controllers;

use App\Models\PERModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PERController extends BaseController
{
    public function __construct(private PERModel $perModel) {}

    public function handlePostPER(Request $request, Response $response): Response
    {
        //? Step 1 - parse body using getParsedBody
        $body = (array) $request->getParsedBody();

        //? Step 2 - list required fields
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
