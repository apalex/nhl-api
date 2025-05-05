<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\PDOService;
use App\Validation\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles user authentication and registration.
 */
class AuthController
{
    private PDOService $db;

    /**
     * AuthController constructor.
     *
     * @param PDOService $db Custom PDO wrapper for managing database connection.
     */
    public function __construct(PDOService $db)
    {
        $this->db = $db;
    }

    /**
     * Registers a user by validating input, hashing the password, and inserting user info.
     *
     * @param Request $request The HTTP request containing user input.
     * @param Response $response The HTTP response object to write to.
     *
     * @throws \Exception If validation fails or user already exists.
     *
     * @return Response The HTTP response with a success message.
     */
    public function register(Request $request, Response $response): Response
    {
        $data = (array) $request->getParsedBody();

        $v = new Validator($data);
        $v->rule('required', ['email', 'password', 'username', 'role']);
        $v->rule('email', 'email');

        if (!$v->validate()) {
            $errors = $v->errors();
            throw new \Exception(json_encode($errors), 422);
        }

        $stmt = $this->db->getPDO()->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);

        if ($stmt->fetch()) {
            throw new \Exception("Email already exists", 422);
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $this->db->getPDO()->prepare(
            "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['username'],
            $data['email'],
            $passwordHash,
            $data['role']
        ]);

        $response->getBody()->write(json_encode(['message' => 'User registered successfully']));
        return $response->withHeader('Content-Type', 'application/json');
    }

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
    public function login(Request $request, Response $response): Response
    {
        $data = (array) $request->getParsedBody();

        if (!isset($data['email'], $data['password'])) {
            throw new \Exception("Missing email or password", 400);
        }

        $stmt = $this->db->getPDO()->prepare("SELECT user_id, password, role FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            throw new \Exception("User not found", 401);
        }
        if (!password_verify($data['password'], $user['password'])) {
            throw new \Exception("Invalid email or password", 401);
        }

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
