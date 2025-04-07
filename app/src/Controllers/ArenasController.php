<?php

namespace App\Controllers;

use App\Exceptions\HttpInvalidIDException;
use App\Exceptions\HttpInvalidInputException;
use App\Models\ArenasModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use App\Services\ArenasService;

/**
 * Controller for handling arena-related requests.
 */
class ArenasController extends BaseController
{
    /**
     * @var ArenasModel $arenas_model The model handling arena data.
     */
    public function __construct(private ArenasModel $arenas_model, private ArenasService $arenasService) {}

    /**
     * Handles requests to retrieve multiple arenas with optional filters.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     * @return Response The JSON response containing arena data.
     */
    public function handleGetArenas(Request $request, Response $response): Response
    {
        // Validate HTTP Method Sent
        $this->validateHTTPMethod($request);

        // Extract query parameters (filters)
        $filters = $request->getQueryParams();

        //* Validate Pagination
        $this->validatePagination($request);

        // Validate Sorting
        if (isset($filters['sort_by'])) {
            $this->validateSortBy($filters['sort_by'], $request);
        }

        // Validate Ordering
        if (isset($filters['order_by'])) {
            $this->validateOrderBy($filters['order_by'], $request);
        }

        // Validate Arena Name
        if (isset($filters['arena_name'])) {
            $this->validateArenaName($filters['arena_name'], $request);
        }

        // Validate Year Built
        if (isset($filters['year_built'])) {
            $this->validateArenaName($filters['year_built'], $request);
        }
        // Validate Arena Capacity
        if (isset($filters['capacity'])) {
            $this->validateArenaName($filters['capacity'], $request);
        }
        // Retrieve the list of arenas
        $arenas = $this->arenas_model->getArenas($filters);

        // Validate Arena Data
        $this->validateArenaInfo($arenas, $request);

        //* Valid HTTP Response Model
        $status = array(
            "Type" => "successful",
            "Code" => 200,
            "Content-Type" => "application/json",
            "Message" => "Arenas fetched successfully",
        );
        $arenas["status"] = $status;
        $arenas = array_reverse($arenas);

        return $this->renderJson($response, $arenas);
    }

    /**
     * Handles requests to retrieve an arena by ID.
     *
     * @param Request $request The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param array $uri_args The URI arguments (e.g., arena_id).
     * @return Response The JSON response containing the arena details.
     */
    public function  handleGetArenasByID(Request $request, Response $response, array $uri_args): Response
    {
        // Validate HTTP Method Sent
        $this->validateHTTPMethod($request);

        // Extract and validate arena_id
        $arena_id = $uri_args["arena_id"];

        if (!ctype_digit($arena_id)) {
            throw new HttpInvalidIDException($request, "The provided arena ID is invalid. Expected Format: int|number");
        }

        // Retrieve the arena details
        $arena = $this->arenas_model->getArenaByID($arena_id);

        // Validate Arena
        if (!$arena) {
            throw new HttpNotFoundException($request, "No matching record for arena info found.");
        }

        //* Valid HTTP Response Model
        return $this->renderJson(
            $response,
            [
                "status" => array(
                    "Type" => "successful",
                    "Code" => 200,
                    "Content-Type" => "application/json",
                    "Message" => "Arena details fetched successfully",
                ),
                "arena" => $arena
            ]
        );
    }

    /**
     * Handles requests to retrieve arenas for a specific team ID.
     *
     * @param Request $request The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param array $uri_args Route arguments containing team ID.
     * @return Response The JSON response containing related arenas.
     */
    public function handleGetArenasGames(Request $request, Response $response, array $uri_args): Response
    {

        // Validate HTTP Method Sent
        $this->validateHTTPMethod($request);

        $arena_id = $uri_args['arena_id'];

        $filters = $request->getQueryParams();

        //* Validate Pagination
        $this->validatePagination($request);

        // Validate Arena ID
        if (!ctype_digit($arena_id)) {
            throw new HttpInvalidInputException($request, "Invalid arena_id. It must be a numeric value.");
        }

        //Validate Game Type
        if (isset($filters['game_type'])) {
            $game_type = $filters['game_type'];
            $this->validateGameType($game_type, $request);
        }

        $arena_info = $this->arenas_model->getGamesByArenaId($arena_id, $filters);

        //* Call Validate Player Info
        $this->validateArenasGame($arena_info, $request);

        return $this->renderJson($response, [
            "status" => array(
                "Type" => "successful",
                "Code" => 200,
                "Content-Type" => "application/json",
                "Message" => "Arena games fetched successfully",
            ),
            "details" => $arena_info
        ]);
    }

    /**
     * Validates the sorting parameter.
     *
     * @param string $sortBy Sorts the allowed fields
     * @param Request $request The HTTP request.
     */
    private function validateSortBy(string $sortBy, Request $request)
    {
        $allowedFields = ['arena_name', 'capacity', 'year_built'];
        if (!in_array($sortBy, $allowedFields)) {
            throw new HttpInvalidInputException($request, "Invalid sort_by parameter. Allowed: " . implode(", ", $allowedFields));
        }
    }

    /**
     * Validates the order parameter.
     *
     * @param string $orderBy The sorting order to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If the order parameter is not valid.
     */
    private function validateOrderBy(string $orderBy, Request $request)
    {
        $allowedOrders = ['asc', 'desc'];
        if (!in_array(strtolower($orderBy), $allowedOrders)) {
            throw new HttpInvalidInputException($request, "Invalid order_by parameter. Allowed: ASC, DESC");
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
     * Validates the arena name format.
     *
     * @param string $arenaName The arena name to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If the arena name contains invalid characters or is empty.
     */
    private function validateArenaName(string $arenaName, Request $request)
    {
        if (empty($arenaName) || !preg_match('/^[a-zA-Z0-9 ]+$/', $arenaName)) {
            throw new HttpInvalidInputException($request, "Invalid arena name. Only letters, numbers, and spaces are allowed.");
        }
    }

    /**
     * Validates if arena data is available.
     *
     * @param array $arenas The array containing arena data.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If no matching arena records are found.
     */
    private function validateArenaInfo(array $arenas, Request $request)
    {
        if (count($arenas['data']) <= 0) {
            throw new HttpInvalidInputException($request, "No matching record for arenas found.");
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
            $this->arenas_model->setPaginationOptions($filters['page'], $filters['page_size']);
        }

        // Check if page or page_size is bigger than current amount in database
    }

    /**
     * Validates if arena game information is available.
     *
     * @param mixed $arena_info The arena game information to validate.
     * @param Request $request The HTTP request.
     *
     * @throws HttpInvalidInputException If no arena game record is found.
     */
    private function validateArenasGame($arena_info, Request $request)
    {
        if (count($arena_info['games']) <= 0) {
            //! no matching record in the db
            throw new HttpInvalidInputException($request, "No matching record for arena games found.");
        }
    }


    //* ROUTE: POST /arena

    /**
     * Handles inserting arena(s) into database.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     *
     * @return Response JSON response containing HTTP response to the request.
     */
    public function handlePostArenas(Request $request, Response $response): Response
    {
        //* Validate HTTP Method Sent
        $this->validateHTTPMethod($request, ['POST']);

        //* Fetch Body
        $arena_info = $request->getParsedBody();

        //* Send Body to Service
        $result = $this->arenasService->createArenas($arena_info);

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
                "arena(s)" => $result->getData()
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




    //* ROUTE: DELETE /arena

    /**
     * Handles deleting arena(s) from database.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     *
     * @return Response JSON response containing HTTP response to the request.
     */
    public function handleDeleteArenas(Request $request, Response $response): Response
    {
        //* Validate HTTP Method Sent
        $this->validateHTTPMethod($request, ['DELETE']);

        //* Fetch Body
        $arena_info = $request->getParsedBody();

        //* Send Body to Service
        $result = $this->arenasService->deleteArenas($arena_info);

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
                "arena(s)" => $result->getData()
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
    //* ROUTE: PUT /arenas

    /**
     * Handles updating arena(s) into database.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     *
     * @return Response JSON response containing HTTP response to the request.
     */
    public function handlePutArenas(Request $request, Response $response): Response
    {
        //* Validate HTTP Method Sent
        $this->validateHTTPMethod($request, ['PUT']);

        //* Fetch Body
        $arena_info = $request->getParsedBody();

        //* Send Body to Service
        $result = $this->arenasService->updateArenas($arena_info);

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
                "arena(s)" => $result->getData()
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
}
