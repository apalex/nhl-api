<?php

namespace App\Controllers;

use App\Exceptions\HttpInvalidIDException;
use App\Exceptions\HttpInvalidInputException;
use App\Models\GamesModel;
use App\Services\GamesService;
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
     * @var GamesService $games_service The service that handles validations.
     */
    public function __construct(private GamesModel $games_model, private GamesService $games_service) {}

    /**
     * Handles requests to retrieve multiple games with optional filters.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     * @return Response The JSON response containing game data.
     */
    public function handleGetGames(Request $request, Response $response): Response
    {   // Validate HTTP Method Sent
        $this->validateHTTPMethod($request);
        //? Step 1 - Extract the list of filters.
        $filters = $request->getQueryParams();

        //* Validate Pagination
        $this->validatePagination($request);

        // Validate Sort By
        if (isset($filters['sort_by'])) {
            $this->validateSortBy($filters['sort_by'], $request);
        }

        // Validate Order By
        if (isset($filters['order_by'])) {
            $this->validateOrderBy($filters['order_by'], $request);
        }

        //? Step 2 - Retrieve the list of games.
        $games = $this->games_model->getGames($filters);

        //* Validate Date
        if (isset($filters['game_date'])) {
            $game_date = $filters['game_date'];
            $this->validateDate($game_date, $request);
        }

        //* Validate Game Type
        if (isset($filters['game_type'])) {
            $game_type = $filters['game_type'];
            $this->validateGameType($game_type, $request);
        }

        // Validate Game Info
        $this->validateGameInfo($games, $request);

        //* Valid HTTP Response Model
        $status = array(
            "Type" => "successful",
            "Code" => 200,
            "Content-Type" => "application/json",
            "Message" => "Games fetched successfully",
        );
        $games["status"] = $status;
        $games = array_reverse($games);
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

        //? Validate HTTP Method Sent
        $this->validateHTTPMethod($request);

        //? Step 1 - Retrieve the game ID from the request.
        $game_id = $uri_args["game_id"];

        //? Step 2 - Validate the game ID format.
        $regex_game_id = '/^\d{1,9}$/';
        if (preg_match($regex_game_id, $game_id) === 0) {
            throw new HttpInvalidInputException($request, "The provided game ID is invalid!");
        }

        //? Step 3 - Retrieve the game details.
        $game_info = $this->games_model->getGamesById($game_id);

        //? Validate Game
        $this->validateGame($game_info, $request);

        //? Step 4 - Handle Errors
        if (!$game_info) {
            throw new HttpNotFoundException($request, "The provided game ID is not found!");
        }

        //? Step 5 - Return the JSON response.
        return $this->renderJson(
            $response,
            [
                "status" => array(
                    "Type" => "successful",
                    "Code" => 200,
                    "Content-Type" => "application/json",
                    "Message" => "Game details fetched successfully",
                ),
                "game" => $game_info
            ]
        );
    }

    /**
     * Handles the DELETE request to remove a game by ID.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The HTTP response to return.
     * @param array $uri_args The URI arguments (should include 'game_id').
     * @return Response The JSON response indicating the result of the deletion.
     */
    public function handleDeleteGame(Request $request, Response $response, array $uri_args): Response
    {
        $this->validateHTTPMethod($request, ["DELETE"]);

        //? Step 1 - Retrieve and validate game ID
        $game_id  = $uri_args["game_id"] ?? null;

        //? Step 2 - Retrieve Game Service class
        $result = $this->games_service->deleteGame($game_id);

        //? Step 3 - Make valid http error response
        if (!$result->isSuccess()) {

            $message = $result->getMessage();
            $code = str_contains(strtolower($message), 'not found') ? 404 : 400;

            return $this->renderJson($response, [
                "status" => [
                    "Type" => "error",
                    "Code" => $code,
                    "Content-Type" => "application/json",
                    "Message" => $message,
                    "Errors" => $result->getErrors(),
                ]
            ], $code);
        }

        //? Step 4 - Return response
        return $this->renderJson($response, [
            "status" => [
                "Type" => "successful",
                "Code" => 200,
                "Content-Type" => "application/json",
                "Message" => $result->getMessage(),
            ],
            "data" => $result->getData()
        ], 200);
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
        //? Validate HTTP Method Sent
        $this->validateHTTPMethod($request);

        //? Step 1 - Retrieve the game ID from the request.
        $game_id = $uri_args["game_id"];

        //? Step 2 - Validate the game ID format.
        $regex_game_id = '/^\d{1,9}$/';
        if (!preg_match($regex_game_id, $game_id)) {
            throw new HttpInvalidInputException($request, "The provided game ID is invalid!");
        }

        //? Step 3 - Extract query parameters and apply pagination.
        $filters = $request->getQueryParams();
        $this->validateFilters($filters, $request);

        //* Validate Pagination
        $this->validatePagination($request);

        //? Step 4 - Retrieve stats from the model.
        $stats = $this->games_model->getStatsByGameId($game_id, $filters);

        //? Step 5 - Handle Errors.
        if (count($stats['player_statistics']) <= 0) {
            //! no matching record in the db
            throw new HttpInvalidInputException($request, "No matching record for statistics found for this player.");
        }

        //? Step 6 - Return the JSON response.
        return $this->renderJson(
            $response,
            [
                "status" => array(
                    "Type" => "successful",
                    "Code" => 200,
                    "Content-Type" => "application/json",
                    "Message" => "Game stats fetched successfully",
                ),
                "games" => $stats
            ]
        );
    }

    /**
     * Validates the sort by format.
     *
     * @param string $sorted_filter The sort filter to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If the sort by filter is not valid.
     */
    private function validateSortBy(mixed $sorted_filter, Request $request)
    {
        $allowed = ['game_date', 'game_type', 'side_start', 'home_score', 'away_score'];

        if (!in_array($sorted_filter, $allowed)) {
            //! provided sort filter invalid
            throw new HttpInvalidInputException($request, "The provided sort by filter is invalid. Expected input: ['game_date', 'game_type', 'side_start', 'home_score ', 'away_score']");
        }
    }

    /**
     * Validates the order by format.
     *
     * @param string $order_by The order by to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If the order by filter is not valid.
     */
    private function validateOrderBy(mixed $order_by, Request $request)
    {
        $allowed = ['asc', 'desc'];
        if (!in_array(strtolower($order_by), $allowed)) {
            //! provided order by filter invalid
            throw new HttpInvalidInputException($request, "The provided order by filter is invalid. Expected input: ASC/asc or DESC/desc");
        }
    }

    /**
     * Validates if game information is available.
     *
     * @param mixed $game_info The game information to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If no game record is found.
     */
    private function validateGameInfo($game_info, Request $request)
    {
        if (count($game_info['data']) <= 0) {
            //! no matching record in the db
            throw new HttpInvalidInputException($request, "No matching record for game info found.");
        }
    }

    /**
     * Validates if game singleton information is available.
     *
     * @param mixed $game_info The game information to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If no game record is found.
     */
    private function validateGame($game_info, Request $request)
    {
        if (count($game_info) == 0) {
            //! no matching record in the db
            throw new HttpInvalidInputException($request, "No matching record for game found.");
        }
    }

    /**
     * Validates date format.
     *
     * @param string $game_date The date of game to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidIDException If the date format is incorrect.
     */
    private function validateDate(string $game_date, Request $request)
    {
        $regex_date = '/^\d{4}-\d{2}-\d{2}$/';

        if (preg_match($regex_date, $game_date) === 0) {
            //! provided date of birth invalid
            throw new HttpInvalidIDException(
                $request,
                "The provided date is invalid. Expected format: YYYY-MM-DD"
            );
        }
    }

    /**
     * Validates the game type format.
     *
     * @param string $game_type The game type to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If the game type is not valid.
     */
    private function validateGameType(mixed $game_type, Request $request)
    {
        $allowed = ['regular', 'playoffs', 'preseason'];

        if (!in_array($game_type, $allowed)) {
            //! provided sort filter invalid
            throw new HttpInvalidInputException($request, "The provided game type is invalid. Expected input: ['regular', 'playoffs', 'preseason']");
        }
    }

    /**
     * Validates the filters provided in the request.
     *
     * @param array $filters The filters to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If any filter is invalid.
     */
    private function validateFilters(array $filters, Request $request): void
    {
        //! First Name Validation (must be a non-numeric string or should be not empty)
        if (
            isset($filters['first_name']) &&
            (empty(trim($filters['first_name'])) || is_numeric($filters['first_name']))
        ) {
            throw new HttpInvalidInputException($request, "Invalid First Name. Expected a non-empty string containing alphabetical characters.");
        }

        //! Goals Scored Validation (integer, greater than or equal to zero)
        if (isset($filters['goals_scored']) && (!is_numeric($filters['goals_scored']) || $filters['goals_scored'] < 0)) {
            throw new HttpInvalidInputException($request, "Invalid Goals Scored value. Expected a non-negative integer.");
        }

        //! Assists Validation (integer, greater than or equal to zero)
        if (isset($filters['assists']) && (!is_numeric($filters['assists']) || $filters['assists'] < 0)) {
            throw new HttpInvalidInputException($request, "Invalid Assists value. Expected a non-negative integer.");
        }

        //! SOG Validation (integer, greater than or equal to zero)
        if (isset($filters['sog']) && (!is_numeric($filters['sog']) || $filters['sog'] < 0)) {
            throw new HttpInvalidInputException($request, "Invalid SOG value. Expected a non-negative integer.");
        }
    }

    /**
     * Validates the provided pagination details.
     *
     * @param Request $request The request object for error handling.
     *
     * @throws HttpInvalidInputException If the pagination is not valid.
     */
    private function validatePagination(Request $request)
    {
        $filters = $request->getQueryParams();

        // Check if page or page_size is present in URI
        if (isset($filters['page']) || isset($filters['page_size'])) {
            if (!isset($filters['page'])) {
                //! page must be present in the URI
                throw new HttpInvalidInputException($request, "The 'page' parameter must be present in the URI.");
            }
            if (!isset($filters['page_size'])) {
                //! page_size must be present in the URI
                throw new HttpInvalidInputException($request, "The 'page_size' parameter must be present in the URI.");
            }
        }

        // Check if page parameter is a number
        if (isset($filters['page']) && !is_numeric($filters['page'])) {
            //! provided page invalid
            throw new HttpInvalidInputException($request, "The 'page' parameter must be a valid number.");
        }

        // Check if page_size parameter is a number
        if (isset($filters['page_size']) && !is_numeric($filters['page_size'])) {
            //! provided page size invalid
            throw new HttpInvalidInputException($request, "The 'page_size' parameter must be a valid number.");
        }

        // Check if page parameter is greater than zero
        if (isset($filters['page']) && $filters['page'] < 1) {
            //! provided page must be greater than zero
            throw new HttpInvalidInputException($request, "The 'page' parameter must be greater than zero.");
        }

        // Check if page_size parameter is greater than zero
        if (isset($filters['page_size']) && $filters['page_size'] < 1) {
            //! provided page_size number must be greater than zero
            throw new HttpInvalidInputException($request, "The 'page_size' parameter must be greater than zero or must be present in the URI.");
        }

        // Check if page and page_size parameters are present inside URI
        if (isset($filters['page']) && isset($filters['page_size'])) {
            $this->games_model->setPaginationOptions($filters['page'], $filters['page_size']);
        }

        // Check if page or page_size is bigger than current amount in database
    }
}
