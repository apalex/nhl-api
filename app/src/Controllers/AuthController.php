<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Helpers\PasswordTrait;
use App\Helpers\LogHelper;
use Valitron\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


/**
 * Handles user authentication and registration.
 */
class AuthController {
    use PasswordTrait;

    private $db;
    private $jwtSecret;

    /**
     * Constructor to initialize dependencies.
     *
     * @param \Psr\Container\ContainerInterface $container Dependency container.
     */
    public function __construct($container) {
        $this->db = $container->get('db');
        $this->jwtSecret = $_ENV['JWT_SECRET'];
    }


    /**
     * Handles user registration by validating input, hashing the password, and storing user info.
     *
     * @param Request $request The HTTP request containing user input.
     * @param Response $response The HTTP response object to write to.
     *
     * @throws \Exception If validation fails or user already exists.
     *
     * @return Response The HTTP response with a success message.
     */
    public function register(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $v = new Validator($data);
        $v->rule('required', ['email', 'password', 'username', 'role']);
        $v->rule('email', 'email');

        if (!$v->validate()) {
            $errors = $v->errors();
            throw new \Exception(json_encode($errors), 422);
        }

        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            throw new \Exception("Email already exists", 422);
        }

        $passwordHash = $this->hashPassword($data['password']);
        $stmt = $this->db->prepare("INSERT INTO ws_users (email, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['email'], $data['username'], $passwordHash, $data['role']]);

        LogHelper::logAccess([
            'method' => 'POST',
            'uri' => '/register',
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user' => $data['email']
        ]);

        $response->getBody()->write(json_encode(['message' => 'Account created successfully']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }


    /**
     * Authenticates a user and generates a JWT on successful login.
     *
     * @param Request $request The HTTP request containing login credentials.
     * @param Response $response The HTTP response object to write to.
     *
     * @throws \Exception If validation fails or credentials are incorrect.
     *
     * @return Response The HTTP response containing a JWT token.
     */
    public function login(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        $v = new Validator($data);
        $v->rule('required', ['email', 'password']);

        if (!$v->validate()) {
            throw new \Exception(json_encode($v->errors()), 422);
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();

        if (!$user || !$this->verifyPassword($data['password'], $user['password'])) {
            LogHelper::logAccess([
                'method' => 'POST',
                'uri' => '/login',
                'ip' => $_SERVER['REMOTE_ADDR'],
                'user' => $data['email']
            ]);
            throw new \Exception("Invalid credentials", 401);
        }

        $payload = [
            'user_id' => $user['user_id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + 3600
        ];

        $token = JWT::encode($payload, $this->jwtSecret, 'HS256');

        LogHelper::logAccess([
            'method' => 'POST',
            'uri' => '/login',
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user' => $user['email']
        ]);

        $response->getBody()->write(json_encode(['token' => $token]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
