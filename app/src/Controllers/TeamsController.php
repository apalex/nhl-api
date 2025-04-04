<?php

namespace App\Controllers;

use App\Core\AppSettings;
use App\Exceptions\HttpInvalidIDException;
use App\Exceptions\HttpInvalidInputException;
use App\Services\TeamsService;
use App\Models\TeamsModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class TeamsController
 *
 * Handles requests related to team data, including retrieving team info and team games
 */
class TeamsController extends BaseController
{

    /**
     * TeamsController constructor
     *
     * @param TeamsModel $teamsModel The teams model instance.
     * @param TeamsService $teamsService The teams service instance.
     */
    public function __construct(private TeamsModel $teamsModel, private TeamsService $teamsService) {}

    //* ROUTE: GET /teams

    /**
     * Handles retrieving a list of teams with optional filtering, sorting, and pagination.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     *
     * @throws HttpInvalidInputException If query parameters `page` or `page_size` are not numeric.
     *
     * @return Response JSON response containing team details for the match.
     */
    public function handleGetTeams(Request $request, Response $response, array $uri_args): Response
    {
        //* Validate HTTP Method Sent
        $this->validateHTTPMethod($request);

        $filters = $request->getQueryParams();

        //* Validate Pagination
        $this->validatePagination($request);

        //* Validate Sort By
        if (isset($filters['sort_by'])) {
            $this->validateSortBy($filters['sort_by'], $request);
        }

        //* Validate Order By
        if (isset($filters['order_by'])) {
            $this->validateOrderBy($filters['order_by'], $request);
        }

        //* Validate Founding Year
        if (isset($filters['founding_year'])) {
            $this->validateFoundingYear($filters['founding_year'], $request);
        }

        $teams = $this->teamsModel->getTeams($filters);

        //* Validate Team Info
        $this->validateTeamInfo($teams, $request);

        //* Valid HTTP Response Model
        $status = array(
            "Type" => "successful",
            "Code" => 200,
            "Content-Type" => "application/json",
            "Message" => "Teams fetched successfully",
        );
        $teams["status"] = $status;
        $teams = array_reverse($teams);
        return $this->renderJson($response, $teams);
    }

    /**
     * Handles the request to retrieve the team by the specific ID.
     *
     * @param Request $request The HTTP request.
     * @param Response $response The HTTP response.
     * @param array $uri_args URL parameters including team ID.
     *
     * @return Response JSON response with team details.
     *
     * @throws HttpInvalidIDException If the team ID format is invalid.
     */
    public function handleGetTeamByID(Request $request, Response $response, array $uri_args): Response
    {
        //* Validate HTTP Method Sent
        $this->validateHTTPMethod($request);

        $team_id = $uri_args['team_id'];

        //* Validate Team ID
        $this->validateTeamID($team_id, $request);

        $team_info = $this->teamsModel->getTeamByID($team_id);

        //* Validate Team
        $this->validateTeam($team_info, $request);

        //* Valid HTTP Response Model
        return $this->renderJson(
            $response,
            [
                "status" => array(
                    "Type" => "successful",
                    "Code" => 200,
                    "Content-Type" => "application/json",
                    "Message" => "Team details fetched successfully",
                ),
                "team" => $team_info
            ]
        );
    }

    /**
     * Handles the request to retrieve the team goals by specific ID.
     *
     * @param Request $request The HTTP request.
     * @param Response $response The HTTP response.
     * @param array $uri_args URL parameters including team ID.
     *
     * @return Response JSON response with team goal statistics.
     *
     * @throws HttpInvalidIDException If the team ID format is invalid.
     */
    public function handleGetTeamGames(Request $request, Response $response, array $uri_args): Response
    {
        //* Validate HTTP Method Sent
        $this->validateHTTPMethod($request);

        $team_id = $uri_args['team_id'];

        //* Validate Team ID
        $this->validateTeamID($team_id, $request);

        $filters = $request->getQueryParams();

        //* Validate Pagination
        $this->validatePagination($request);

        $team_info = $this->teamsModel->getTeamGames($team_id, $filters);

        //* Validate Date
        if (isset($filters['date'])) {
            $game_date = $filters['date'];
            $this->validateDate($game_date, $request);
        }

        //* Validate Game Type
        if (isset($filters['game_type'])) {
            $game_type = $filters['game_type'];
            $this->validateGameType($game_type, $request);
        }

        //* Validate Side Start
        if (isset($filters['side_start'])) {
            $side_start = $filters['side_start'];
            $this->validateSideStart($side_start, $request);
        }

        //* Call Validate Player Info
        $this->validateTeamGame($team_info, $request);

        return $this->renderJson($response, [
            "status" => array(
                "Type" => "successful",
                "Code" => 200,
                "Content-Type" => "application/json",
                "Message" => "Team games fetched successfully",
            ),
            "details" => $team_info
        ]);
    }

    //* ROUTE: POST /teams

    /**
     * Handles inserting team(s) into database.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     *
     * @return Response JSON response containing HTTP response to the request.
     */
    public function handlePostTeams(Request $request, Response $response): Response
    {
        //* Validate HTTP Method Sent
        $this->validateHTTPMethod($request, ['POST']);

        //* Fetch Body
        $team_info = $request->getParsedBody();

        //* Send Body to Service
        $result = $this->teamsService->createTeams($team_info);

        //* Valid HTTP Response Message Structure
        if ($result->isSuccess()) {
            $status = [
                "Type" => "successful",
                'Code' => 201,
                "Content-Type" => "application/json",
                'Message' => $result->getMessage()
            ];
            $payload = [
                "status" => $status,
                "team(s)" => $result->getData()
            ];
            return $this->renderJson($response, $payload, 201);
        }
        //* Invalid HTTP Response Message Structure
        else {
            $status = [
                "Type" => "error",
                'Code' => 422,
                'Content-Type' => 'application/json',
                'Message' => $result->getMessage()
            ];
            $payload = [
                "status" => $status,
                "details" => $result->getErrors()
            ];
            return $this->renderJson($response, $payload, 422);
        }
    }

    //* ROUTE: PUT /teams

    /**
     * Handles updating team(s) into database.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     *
     * @return Response JSON response containing HTTP response to the request.
     */
    public function handlePutTeams(Request $request, Response $response): Response
    {
        //* Validate HTTP Method Sent
        $this->validateHTTPMethod($request, ['PUT']);

        //* Fetch Body
        $team_info = $request->getParsedBody();

        //* Send Body to Service
        $result = $this->teamsService->updateTeams($team_info);

        //* Valid HTTP Response Message Structure
        if ($result->isSuccess()) {
            $status = [
                "Type" => "successful",
                'Code' => 200,
                "Content-Type" => "application/json",
                'Message' => $result->getMessage()
            ];
            $payload = [
                "status" => $status,
                "team(s)" => $result->getData()
            ];
            return $this->renderJson($response, $payload, 200);
        }
        //* Invalid HTTP Response Message Structure
        else {
            $status = [
                "Type" => "error",
                'Code' => 422,
                'Content-Type' => 'application/json',
                'Message' => $result->getMessage()
            ];
            $payload = [
                "status" => $status,
                "details" => $result->getErrors()
            ];
            return $this->renderJson($response, $payload, 422);
        }
    }

    //* ROUTE: DELETE /teams

    /**
     * Handles deleting team(s) into database.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     *
     * @return Response JSON response containing HTTP response to the request.
     */
    public function handleDeleteTeams(Request $request, Response $response): Response
    {
        //* Validate HTTP Method Sent
        $this->validateHTTPMethod($request, ['DELETE']);

        //* Fetch Body
        $team_info = $request->getParsedBody();

        //* Send Body to Service
        $result = $this->teamsService->deleteTeams($team_info);

        //* Valid HTTP Response Message Structure
        if ($result->isSuccess()) {
            $status = [
                "Type" => "successful",
                'Code' => 200,
                "Content-Type" => "application/json",
                'Message' => $result->getMessage()
            ];
            $payload = [
                "status" => $status,
                "team(s)" => $result->getData()
            ];
            return $this->renderJson($response, $payload, 200);
        }
        //* Invalid HTTP Response Message Structure
        else {
            $status = [
                "Type" => "error",
                'Code' => 422,
                'Content-Type' => 'application/json',
                'Message' => $result->getMessage()
            ];
            $payload = [
                "status" => $status,
                "details" => $result->getErrors()
            ];
            return $this->renderJson($response, $payload, 422);
        }
    }

    /**
     * Validates the format of a team ID.
     *
     * @param string $team_id The team ID to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidIDException If the team ID format is invalid.
     */
    private function validateTeamID(string $team_id, Request $request)
    {
        $regex_team = '/^\d{1,6}$/';

        if (preg_match($regex_team, $team_id) === 0) {
            //! provided id invalid
            throw new HttpInvalidIDException(
                $request,
                "The provided team id is invalid. Expected Format: int|number"
            );
        }
    }

    /**
     * Validates if team information is available.
     *
     * @param mixed $team_info The team information to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If no team record is found.
     */
    private function validateTeamInfo($team_info, Request $request)
    {
        if (count($team_info['data']) <= 0) {
            //! no matching record in the db
            throw new HttpInvalidInputException($request, "No matching record for team info found.");
        }
    }

    /**
     * Validates if team game information is available.
     *
     * @param mixed $team_info The team game information to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If no team game record is found.
     */
    private function validateTeamGame($team_info, Request $request)
    {
        if (count($team_info['goals']) <= 0) {
            //! no matching record in the db
            throw new HttpInvalidInputException($request, "No matching record for team games found.");
        }
    }

    /**
     * Validates if team singleton information is available.
     *
     * @param mixed $team_info The team information to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If no team record is found.
     */
    private function validateTeam($team_info, Request $request)
    {
        if (count($team_info) == 0) {
            //! no matching record in the db
            throw new HttpInvalidInputException($request, "No matching record for team found.");
        }
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
        $allowed = ['team_name', 'founding_year', 'championships', 'general_manager', 'abbreviation'];

        if (!in_array($sorted_filter, $allowed)) {
            //! provided sort filter invalid
            throw new HttpInvalidInputException($request, "The provided sort by filter is invalid. Expected input: ['team_name', 'founding_year', 'championships', 'general_manager', 'abbreviation']");
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
            $this->teamsModel->setPaginationOptions($filters['page'], $filters['page_size']);
        }

        // Check if page or page_size is bigger than current amount in database
    }

    /**
     * Validates founding_year format.
     *
     * @param string $founding_year The founding_year of team to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidIDException If the founding year format is incorrect.
     */
    private function validateFoundingYear(string $founding_year, Request $request)
    {
        $regex_year = '/^\d{4}$/';

        if (preg_match($regex_year, $founding_year) === 0) {
            //! provided founding year invalid
            throw new HttpInvalidIDException(
                $request,
                "The provided founding year is invalid. Expected format: number|int"
            );
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
     * Validates the side start format.
     *
     * @param string $side_start The side start to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If the side start is not valid.
     */
    private function validateSideStart(mixed $side_start, Request $request)
    {
        $allowed = ['left', 'right'];

        if (!in_array($side_start, $allowed)) {
            //! provided sort filter invalid
            throw new HttpInvalidInputException($request, "The provided side start is invalid. Expected input: ['left', 'right']");
        }
    }
}
