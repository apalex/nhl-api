<?php

namespace App\Controllers;

use App\Exceptions\HttpInvalidIDException;
use App\Exceptions\HttpInvalidInputException;
use App\Models\ArenasModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

/**
 * Controller for handling arena-related requests.
 */
class ArenasController extends BaseController
{
    /**
     * @var ArenasModel $arenas_model The model handling arena data.
     */
    public function __construct(private ArenasModel $arenas_model) {}

    /**
     * Handles requests to retrieve multiple arenas with optional filters.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     * @return Response The JSON response containing arena data.
     */
    public function handleGetArenas(Request $request, Response $response): Response
    {
        // Extract query parameters (filters)
        $filters = $request->getQueryParams();

        // Validate Pagination
        if (isset($filters['page']) && !is_numeric($filters['page'])) {
            throw new HttpInvalidInputException($request, "The 'page' parameter must be a valid number.");
        }

        if (isset($filters['page_size']) && !is_numeric($filters['page_size'])) {
            throw new HttpInvalidInputException($request, "The 'page_size' parameter must be a valid number.");
        }

        if (isset($filters["page"]) && isset($filters["page_size"])) {
            $this->arenas_model->setPaginationOptions(
                $filters["page"],
                $filters["page_size"]
            );
        }

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
    public function  handleGetArenaByID(Request $request, Response $response, array $uri_args): Response
    {
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
    public function handleGetArenaGames(Request $request, Response $response, array $uri_args): Response
    {
        $arena_id = $uri_args['arena_id'];

        // Validate Team ID
        if (!ctype_digit($arena_id)) {
            throw new HttpInvalidInputException($request, "Invalid arena_id. It must be a numeric value.");
        }

        //TODO VALIDATE TOURNAMENT TYPE @DE SOUSA

        $filters = $request->getQueryParams();
        $arena_info = $this->arenas_model->getGamesByArenaId($arena_id, $filters);

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
     */
    private function validateOrderBy(string $orderBy, Request $request)
    {
        $allowedOrders = ['asc', 'desc'];
        if (!in_array(strtolower($orderBy), $allowedOrders)) {
            throw new HttpInvalidInputException($request, "Invalid order_by parameter. Allowed: ASC, DESC");
        }
    }

    /**
     * Validates the arena name format.
     */
    private function validateArenaName(string $arenaName, Request $request)
    {
        if (empty($arenaName) || !preg_match('/^[a-zA-Z0-9 ]+$/', $arenaName)) {
            throw new HttpInvalidInputException($request, "Invalid arena name. Only letters, numbers, and spaces are allowed.");
        }
    }

    /**
     * Validates if arena data is available.
     */
    private function validateArenaInfo($arenas, Request $request)
    {
        if (count($arenas['data']) <= 0) {
            throw new HttpInvalidInputException($request, "No matching record for arenas found.");
        }
    }
}
