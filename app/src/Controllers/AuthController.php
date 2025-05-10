<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Services\LoginService;
use App\Services\RegisterService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles user authentication.
 */
class AuthController extends BaseController
{
    /**
     * AuthController constructor.
     *
     * @var UserModel $user_model The model handling user data.
     * @param LoginService $login_service The login service instance.
     *
     */
    public function __construct(private UserModel $user_model, private LoginService $login_service) {}

    /**
     * Logs in a user by validating credentials and generating a JWT token.
     *
     * @param Request $request The HTTP request with login credentials.
     * @param Response $response The HTTP response object for writing output.
     *
     * @throws \Exception If the credentials are invalid or user not found.
     *
     * @return Response The HTTP response containing the JWT token.
     */
    public function handleLogin(Request $request, Response $response): Response
    {
        //* Validate HTTP Method Sent
        $this->validateHTTPMethod($request, ['POST']);

        //* Fetch Body
        $user_info = $request->getParsedBody();

        //* Send Body to Service
        $user = $this->login_service->authenticate($user_info, $request);

        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'uid' => $user['user_id'],
            'role' => $user['role']
        ];

        $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
