<?php

namespace App\Middleware;

use App\Helpers\LogHelper;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AccessLogMiddleware
 *
 * Middleware that logs every incoming HTTP request using LogHelper.
 * Captures and records the request method, URI, client IP, and query parameters.
 */
class AccessLogMiddleware
{
    /**
     * Invokes the middleware.
     *
     * @param Request $request The incoming HTTP request.
     * @param Handler $handler The next request handler in the middleware stack.
     * @return ResponseInterface The response from the next middleware or route.
     */
    public function __invoke(Request $request, Handler $handler): ResponseInterface
    {
        //* Extract client IP address
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';

        //* Get HTTP method and request URI
        $method = $request->getMethod();
        $uri = (string) $request->getUri();

        //* Get any query parameters from the request
        $params = $request->getQueryParams();

        //* Log access data using LogHelper
        LogHelper::logAccess("Incoming Request", [
            'ip' => $ip,
            'method' => $method,
            'uri' => $uri,
            'params' => $params
        ]);

        //* Continue to the next middleware or route handler
        return $handler->handle($request);
    }
}
