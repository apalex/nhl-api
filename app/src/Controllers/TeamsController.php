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

        //* Validate Pagination

        //* Validate Sort By

        //* Validate Order By

        //* Validate Team Info

        $teams = $this->teamsModel->getTeams($filters);

        return $this->renderJson($response, $teams);
    }

    public function handleGetTeamByID(Request $request, Response $response, array $uri_args): Response
    {
        $team_id = $uri_args['team_id'];

        //* Validate Team ID
        // $this->validateTeamID($team_id, $request);

        $team_info = $this->teamsModel->getTeamByID($team_id);

        //* Validate Team
        // $this->validateTeam($team_info, $request);

        return $this->renderJson($response, $team_info);
    }

    public function handleGetTeamGames(Request $request, Response $response, array $uri_args): Response
    {
        $filters = $request->getQueryParams();

        //* Validate Date

        //* Validate Arena

        //* Validate Tournament Type

        //* Validate Side Start
        $stats = $this->teamsModel->getTeamGames(1, $filters);
        return $this->renderJson($response, $stats);
    }
}
