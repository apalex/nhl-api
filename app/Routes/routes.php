<?php

declare(strict_types=1);

use App\Controllers\AboutController;
use App\Controllers\ArenasController;
use App\Controllers\GamesController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\PossessionController;
use App\Controllers\TeamsController;
use App\Helpers\DateTimeHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\AuthController;
use App\Controllers\AuthenticateController;
use App\Controllers\PERController;
use App\Controllers\SOGPController;
use App\Controllers\CityWeatherController;

return static function (Slim\App $app): void {

    //* ROUTE: GET
    $app->get('/', [AboutController::class, 'handleAboutWebService']);

    $app->get('/teams', [TeamsController::class, 'handleGetTeams']);

    $app->get('/teams/{team_id}', [TeamsController::class, 'handleGetTeamByID']);

    $app->get('/teams/{team_id}/games', [TeamsController::class, 'handleGetTeamGames']);

    $app->get('/games', [GamesController::class, 'handleGetGames']);

    $app->get('/games/{game_id}', [GamesController::class, 'handleGetGameByID']);

    $app->get('/games/{game_id}/stats', [GamesController::class, 'handleGetGameStats']);

    $app->get('/arenas', [ArenasController::class, 'handleGetArenas']);

    $app->get('/arenas/{arena_id}', [ArenasController::class, 'handleGetArenaByID']);

    $app->get('/arenas/{arena_id}/games', [ArenasController::class, 'handleGetArenaGames']);

    $app->get('/city-weather', CityWeatherController::class . ':getCityWeather');

    $app->get('/ping', function (Request $request, Response $response, $args) {

        $payload = [
            "greetings" => "Reporting! Hello there!",
            "now" => DateTimeHelper::now(DateTimeHelper::Y_M_D_H_M),
        ];
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR));
        return $response;
    });

    // Example route to test error handling
    $app->get('/error', function (Request $request, Response $response, $args) {
        throw new \Slim\Exception\HttpNotFoundException($request, "Something went wrong");
    });

    //* ROUTE: POST
    $app->post('/teams', [TeamsController::class, 'handlePostTeams']);

    $app->post('/games', [GamesController::class, 'handleCreateGame']);

    $app->post('/arenas', [ArenasController::class, 'handlePostArenas']);

    $app->post('/computePER', [PERController::class, 'handlePostPER']);

    $app->post('/computeSOGP', [SOGPController::class, 'handlePostSOGP']);

    $app->post('/possession', [PossessionController::class, 'calculatePossession']);
    //* ROUTE: PUT
    $app->put('/teams', [TeamsController::class, 'handlePutTeams']);

    $app->put('/games', [GamesController::class, 'handleUpdateGame']);

    $app->put('/arenas', [ArenasController::class, 'handlePutArenas']);

    //* ROUTE: DELETE
    $app->delete('/teams', [TeamsController::class, 'handleDeleteTeams']);

    $app->delete('/games', [GamesController::class, 'handleDeleteGame']);

    $app->delete('/arenas', [ArenasController::class, 'handleDeleteArenas']);

    //*Login Routes:
    /**
     * Registers a new user.
     */
    $app->post('/register', [RegisterController::class, 'handleRegister']);

    /**
     * Logs in an existing user.
     */
    $app->post('/login', [AuthController::class, 'handleLogin']);
};
