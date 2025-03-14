<?php

namespace App\Controllers;

use App\Exceptions\HttpInvalidInputException;
use App\Models\GamesModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

/**
 * Controller for handling game-related requests.
 */
class GamesController extends BaseController
{
    /**
     * @var GamesModel $games_model The model handling game data.
     */
    public function __construct(private GamesModel $games_model) {}

    /**
     * Handles requests to retrieve multiple games with optional filters.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     * @return Response The JSON response containing game data.
     */
    public function handleGetGames(Request $request, Response $response): Response
    {
        //? Step 1 - Extract the list of filters.
        $filters = $request->getQueryParams();
        if (isset($filters["page"]) && isset($filters["limit"])) {
            $this->games_model->setPaginationOptions(
                $filters["page"],
                $filters["limit"]
            );
        }

        //? Step 2 - Retrieve the list of games.
        $games = $this->games_model->getGames($filters);

        //? Step 3 - Return the JSON response.
        return $this->renderJson($response, $games);
    }

    /**
     * Handles requests to retrieve game details by game ID.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     * @param array $uri_args The URI arguments (e.g., game_id).
     * @return Response The JSON response containing the game details.
     * @throws HttpInvalidInputException If the game ID is invalid.
     * @throws HttpNotFoundException If the game ID is not found.
     */
    public function handleGetGameByID(Request $request, Response $response, array $uri_args): Response
    {
        //? Step 1 - Retrieve the game ID from the request.
        $game_id = $uri_args["game_id"];

        //? Step 2 - Validate the game ID format.
        $regex_game_id = '/^\d{1,9}$/';
        if (preg_match($regex_game_id, $game_id) === 0) {
            throw new HttpInvalidInputException($request, "The provided game ID is invalid!");
        }

        //? Step 3 - Retrieve the game details.
        $game_info = $this->games_model->getGamesById($game_id);

        //? Step 4 - Handle Errors
        if (!$game_info) {
            throw new HttpNotFoundException($request, "The provided game ID is not found!");
        }

        //? Step 5 - Return the JSON response.
        return $this->renderJson($response, $game_info);
    }

    /**
     * Handles requests to retrieve game stats by game ID with filters.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     * @param array $uri_args The URI arguments (e.g., game_id).
     * @return Response The JSON response containing the game stats.
     * @throws HttpInvalidInputException If the game ID is invalid.
     * @throws HttpNotFoundException If no stats are found for the given game.
     */
    public function handleGetGameStats(Request $request, Response $response, array $uri_args): Response
    {
        //? Step 1 - Retrieve the game ID from the request.
        $game_id = $uri_args["game_id"];

        //? Step 2 - Validate the game ID format.
        $regex_game_id = '/^\d{1,9}$/';
        if (!preg_match($regex_game_id, $game_id)) {
            throw new HttpInvalidInputException($request, "The provided game ID is invalid!");
        }

        //? Step 3 - Extract query parameters and apply pagination.
        $filters = $request->getQueryParams();

        if (isset($filters["page"]) && isset($filters["limit"])) {
            $this->games_model->setPaginationOptions(
                $filters["page"],
                $filters["limit"]
            );
        }

        //? Step 4 - Retrieve stats from the model.
        $stats = $this->games_model->getStatsByGameId($game_id, $filters);

        //? Step 5 - Handle Errors.
        if (!$stats) {
            throw new HttpNotFoundException($request, "No goals found for this player!");
        }

        //? Step 6 - Return the JSON response.
        return $this->renderJson($response, $stats);
    }
}