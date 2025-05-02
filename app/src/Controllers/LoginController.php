<?php

namespace App\Controllers;

use App\Exceptions\HttpInvalidIDException;
use App\Exceptions\HttpInvalidInputException;
use App\Models\UserModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

/**
 * Controller for handling register-related requests.
 */
class LoginController extends BaseController
{
    /**
     * @var UserModel $user_model The model handling user data.
     * @var GamesService $games_service The service that handles validations.
     */
    public function __construct(private UserModel $user_model) {}

    //* ROUTE: POST /login

    /**
     * Handles user login.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     *
     * @return Response JSON response containing HTTP response to the request.
     */
    public function handlePostUser(Request $request, Response $response): Response
    {
        //* Validate HTTP Method Sent
        $this->validateHTTPMethod($request, ['POST']);

        // //* Fetch Body
        // $team_info = $request->getParsedBody();

        // //* Send Body to Service
        // $result = $this->teamsService->createTeams($team_info);

        // //* Valid HTTP Response Message Structure
        // if ($result->isSuccess()) {
        //     $status = [
        //         "Type" => "successful",
        //         'Code' => 201,
        //         "Content-Type" => "application/json",
        //         'Message' => $result->getMessage()
        //     ];
        //     $payload = [
        //         "status" => $status,
        //         "team(s)" => $result->getData()
        //     ];
        //     return $this->renderJson($response, $payload, 201);
        // }
        // //* Invalid HTTP Response Message Structure
        // else {
        //     $status = [
        //         "Type" => "error",
        //         'Code' => 422,
        //         'Content-Type' => 'application/json',
        //         'Message' => $result->getMessage()
        //     ];
        //     $payload = [
        //         "status" => $status,
        //         "details" => $result->getErrors()
        //     ];
        return $this->renderJson($response, [], 422);
    }
}
