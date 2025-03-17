<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\ArenaModel;
use App\Core\PDOService;
use App\Exceptions\HttpInvalidInputException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class ArenasController
{
    private ArenasModel $arenaModel;

    /**
     * Constructor for ArenasController.
     *
     * @param PDOService $pdo The PDO service for database interactions.
     */
    public function __construct(PDOService $pdo)
    {
        $this->arenaModel = new ArenasModel($pdo);
    }

    /**
     * Handles retrieving a list of arenas with filtering, sorting, and pagination.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws HttpBadRequestException
     */
    public function handleGetArenas(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();

        try {
            $arenas = $this->arenaModel->getArenas($queryParams);

            $response->getBody()->write(json_encode($arenas));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, "Invalid request parameters.");
        }
    }

    /**
     * Handles retrieving a single arena by ID.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws HttpNotFoundException
     */
    public function handleGetArenaByID(Request $request, Response $response, array $args): Response
    {
        $arenaId = $args['arena_id'];
        $arena = $this->arenaModel->getArenaById($arenaId);

        if (!$arena) {
            throw new HttpInvalidInputException($request, "Arena not found");
        }

        $response->getBody()->write(json_encode($arena));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
