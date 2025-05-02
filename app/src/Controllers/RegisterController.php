<?php

namespace App\Controllers;

use App\Exceptions\HttpInvalidIDException;
use App\Exceptions\HttpInvalidInputException;
use App\Models\UserModel;
use App\Services\RegisterService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

/**
 * Controller for handling register-related requests.
 */
class RegisterController extends BaseController
{
    /**
     * @var UserModel $user_model The model handling user data.
     * @param RegisterService $register_service The register service instance.
     */
    public function __construct(private UserModel $user_model, private RegisterService $register_service) {}

    //* ROUTE: POST /register

    /**
     * Handles inserting user into database.
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

        //* Fetch Body
        $user_info = $request->getParsedBody();

        //* Send Body to Service
        $result = $this->register_service->createUser($user_info);

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
                "user" => $result->getData()
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
}
