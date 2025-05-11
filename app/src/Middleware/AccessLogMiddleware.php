<?php

namespace App\Middleware;

use App\Helpers\LogHelper;
use App\Models\AccessLogModel;
use App\Core\PDOService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AccessLogMiddleware
 *
 * Middleware that logs every incoming HTTP request to both a file and a database.
 *
 */
class AccessLogMiddleware
{
    /**
     * @var AccessLogModel The model used to persist logs to the database.
     */
    protected AccessLogModel $logModel;

    /**
     * AccessLogMiddleware constructor.
     *
     * @param PDOService $pdo The PDO service for database connection.
     */
    public function __construct(PDOService $pdo)
    {
        $this->logModel = new AccessLogModel($pdo);
    }

    /**
     * Middleware invokable class method.
     *
     * This method is triggered for every incoming request. It:
     * - Extracts request details (IP, method, URI, query params).
     * - Gets user info from request attributes (if authenticated).
     * - Logs the access to a file for audit purposes.
     * - Logs to the `logs` table using the defined schema:
     *     - user_id (int)
     *     - username (string)
     *     - http_method (string)
     *     - uri (string)
     *
     * @param Request $request The incoming server request.
     * @param Handler $handler The next middleware/request handler.
     *
     * @return ResponseInterface The response after logging and passing to the next handler.
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

        $user = $request->getAttribute('user') ?? null;

        //* Fallbacks if user is not authenticated
        $userId = is_array($user) && isset($user['id']) ? $user['id'] : 0;
        $username = is_array($user) && isset($user['username']) ? $user['username'] : 'guest';

        //* Log to file
        LogHelper::logAccess("Incoming Request", [
            'ip' => $ip,
            'method' => $method,
            'uri' => $uri,
            'params' => $params,
            'username' => $username
        ]);

        //* Log to database
        $this->logModel->insertLog([
            'user_id' => $userId,
            'username' => $username,
            'http_method' => $method,
            'uri' => $uri,
            'ip_address' => $ip
        ]);

        return $handler->handle($request);
    }
}
