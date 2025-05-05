<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Psr7\Response;


/**
 * Middleware to validate JWT tokens and authorize users.
 */
class JwtMiddleware
{
    private $secret;
    /**
     * Initializes the middleware with the secret key.
     *
     * @param string $secret JWT secret key used for decoding tokens.
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }
    /**
     * Processes incoming requests and validates the JWT token.
     *
     * @param ServerRequestInterface $request Incoming request with Authorization header.
     * @param RequestHandlerInterface $handler The next request handler in the pipeline.
     *
     * @return ResponseInterface Response with 401 if token is invalid or expired, or the next response if valid.
     */
    public function __invoke($request, $handler)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Authorization header missing']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            $request = $request->withAttribute('user', $decoded);
            return $handler->handle($request);
        } catch (\Exception $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid or expired token']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }
}
