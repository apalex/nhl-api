<?php

namespace App\Controllers;

use App\Core\AppSettings;
use App\Models\TeamsModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TeamsController extends BaseController
{
    public function __construct(private TeamsModel $teamsModel) {}

    public function handleGetTeams(Request $request, Response $response, array $uri_args): Response
    {
        $filters = $request->getQueryParams();
        $teams = $this->teamsModel->getTeams($filters);
        return $this->renderJson($response, $teams);
    }

    public function handleGetTeamByID(Request $request, Response $response): Response
    {
        $filters = $request->getQueryParams();
        $team = $this->teamsModel->getTeamByID(1, $filters);
        return $this->renderJson($response, $team);
    }

    public function handleGetTeamPlayerStats(Request $request, Response $response, array $uri_args): Response
    {
        $filters = $request->getQueryParams();
        $stats = $this->teamsModel->getTeamPlayerStats(1, $filters);
        return $this->renderJson($response, $stats);
    }
}
