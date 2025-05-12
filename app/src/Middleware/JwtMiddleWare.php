<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpForbiddenException;

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
    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }
 /**
     * Processes incoming requests and validates the JWT token.
     *
     * @param Request        $request Incoming request with Authorization header.
     * @param RequestHandler $handler The next request handler in the pipeline.
     *
     * @return ResponseInterface Response with 401 if token is invalid or expired,
     *                           403 if not permitted, or the next response if valid.
     *
     * @throws HttpUnauthorizedException If the token is missing, malformed, or invalid.
     * @throws HttpForbiddenException    If the user is not authorized for the HTTP method.
     */
    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        $path   = $request->getUri()->getPath();
        $method = strtoupper($request->getMethod());

        if (in_array($path, ['/login', '/register', '/nhl-api/login', '/nhl-api/register'], true)) {
            return $handler->handle($request);
        }

        $authHeader = $request->getHeaderLine('Authorization');

        // Check for missing Authorization header
        if (empty($authHeader)) {
            throw new HttpUnauthorizedException($request, 'Missing Authorization header');
        }

        // Validate Bearer Token format
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $m)) {
            throw new HttpUnauthorizedException($request, 'Invalid authorization header');
        }

        try {
            $decoded = JWT::decode($m[1], new Key($this->secret, 'HS256'));
        } catch (\Exception $e) {
            throw new HttpUnauthorizedException($request, 'Invalid authorization header');
        }

        $request = $request->withAttribute('user', (array) $decoded);

        $role = $decoded->role ?? '';
        if ($method !== 'GET' && $role !== 'admin') {
            throw new HttpForbiddenException($request, 'You do not have permission to perform this operation');
        }

        return $handler->handle($request);
    }
}