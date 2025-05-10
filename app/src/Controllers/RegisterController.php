<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Services\RegisterService;
use App\Services\LoginService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles user authentication.
 */
class RegisterController extends BaseController
{
    /**
     * RegisterController constructor.
     *
     * @var UserModel $user_model The model handling user data.
     * @param RegisterService $register_service The register service instance.
     *
     */
    public function __construct(private UserModel $user_model, private RegisterService $register_service) {}

    /**
     * Registers a user by validating input, hashing the password, and inserting user info.
     *
     * @param Request $request The HTTP request containing user input.
     * @param Response $response The HTTP response object to write to.
     *
     * @return Response The HTTP response with a success message.
     */
    public function handleRegister(Request $request, Response $response): Response
    {
        //* Validate HTTP Method Sent
        $this->validateHTTPMethod($request, ['POST']);

        //* Fetch Body
        $user_info = $request->getParsedBody();

        //* Send Body to Service
        $result = $this->register_service->createUser($user_info);

        //* Valid HTTP Response Message Structure
        if ($result->isSuccess()) {
            $payload = [
                "Type" => "successful",
                'Code' => 201,
                "Content-Type" => "application/json",
                'Message' => $result->getMessage()
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
            return $this->renderJson($response, $payload, 422)->withHeader('Content-Type', 'application/json');
        }
    }
}
