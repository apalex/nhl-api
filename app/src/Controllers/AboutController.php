<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\AppSettings;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AboutController extends BaseController
{
    private const API_NAME = 'NHL-API';
    private const API_VERSION = '1.0.0';

    public function handleAboutWebService(Request $request, Response $response): Response
    {
        $data = array(
            'api' => self::API_NAME,
            'version' => self::API_VERSION,
            'about' => 'Our project is a Web Services application designed to provide access to NHL data through a custom-built API. This project is developed using PHP and the Slim Framework. Our API offers endpoints for retrieving detailed information about NHL teams, players, games, statistics, and more.',
            'authors' => 'This project is a collaborative effort by our team -- Alex, Michael, and Wayne',
            'resources' => [
                [
                    'uri' => '/teams',
                    'description' => 'Gets the list of teams',
                    'type' => 'collection resources',
                    'filters' => 'Team Name, Founding Year',
                    'sorting' => 'Team Name, Founding Year'
                ],
                [
                    'uri' => '/teams/{team_id}',
                    'description' => 'Gets the details of the specified team',
                    'type' => 'singleton resources',
                    'filters' => 'N/A',
                    'sorting' => 'N/A'
                ],
                [
                    'uri' => '/teams/{team_id}/games',
                    'description' => 'Gets the list of the specified team’s games',
                    'type' => 'sub-collection resources',
                    'filters' => 'Date (condition: greater than), Arena',
                    'sorting' => 'Date, Arena'
                ],
                [
                    'uri' => '/games',
                    'description' => 'Gets a list of games',
                    'type' => 'collection resources',
                    'filters' => 'Date (condition: greater than), Tournament Type',
                    'sorting' => 'Date, Tournament Type'
                ],
                [
                    'uri' => '/games/{game_id}',
                    'description' => 'Gets the details of the specified game',
                    'type' => 'singleton resources',
                    'filters' => 'N/A',
                    'sorting' => 'N/A'
                ],
                [
                    'uri' => '/games/{game_id}/player_stats',
                    'description' => 'Gets the list of player statistics for a specific game',
                    'type' => 'sub-collection resources',
                    'filters' => 'First Name, Goals Scored, Assists, SOG',
                    'sorting' => 'First Name, Goals Scored, Assists, SOG'
                ],
                [
                    'uri' => '/arenas',
                    'description' => 'Gets the list of arenas where NHL games took place',
                    'type' => 'collection resources',
                    'filters' => 'Arena Name, Year Built (condition: greater than), Capacity (greater or lower), City, State/Province',
                    'sorting' => 'Arena Name, Year Built, Capacity, City'
                ],
                [
                    'uri' => '/arenas/{arena_id}',
                    'description' => 'Gets the details of the specified arena',
                    'type' => 'singleton resources',
                    'filters' => 'N/A',
                    'sorting' => 'N/A'
                ],
                [
                    'uri' => '/arenas/{arena_id}/games',
                    'description' => 'Gets the list of games played in the specified arena',
                    'type' => 'sub-collection resources',
                    'filters' => 'Date (condition: greater than), Tournament Type',
                    'sorting' => 'Date, Tournament Type'
                ]
            ]
        );
        return $this->renderJson($response, $data);
    }
}
