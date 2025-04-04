<?php

namespace App\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

/**
 * Custom Error Handler for handling application errors.
 *
 * This handler formats error details into a JSON response
 * with improved error description and stack trace.
 */
class CustomErrorHandler implements ErrorHandlerInterface
{
    /**
     * Invoke method to handle errors and generate a response.
     *
     * @param ServerRequestInterface $request The server request instance.
     * @param Throwable $exception The caught exception.
     * @param bool $displayErrorDetails Flag to determine if error details should be shown.
     * @param bool $logErrors Flag to determine if errors should be logged.
     * @param bool $logErrorDetails Flag to determine if detailed errors should be logged.
     *
     * @return ResponseInterface JSON response containing error details.
     */
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        $response = new \Slim\Psr7\Response();

        $statusCode = $exception->getCode() ?: 500;
        $errorTitle = $this->getErrorTitle($statusCode);
        $errorDescription = $displayErrorDetails
            ? $this->getShortTrace($exception->getTrace())
            : 'An unexpected error occurred.';

        $error = [
            'status' => $statusCode,
            'title' => $errorTitle,
            'message' => $exception->getMessage(),
            'description' => $errorDescription
        ];

        $response->getBody()->write(json_encode($error, JSON_PRETTY_PRINT));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }

    /**
     * Returns an error title based on the HTTP status code.
     *
     * @param int $statusCode The HTTP status code.
     *
     * @return string A human-readable error title.
     */
    private function getErrorTitle(int $statusCode): string
    {
        $titles = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ];

        return $titles[$statusCode] ?? 'Unknown Error';
    }

    /**
     * Generates a shortened stack trace for easier debugging.
     *
     * @param array $trace The full stack trace array.
     * @param int $limit The maximum number of trace lines to display (default is 5).
     *
     * @return string The formatted stack trace as a string.
     */
    private function getShortTrace(array $trace, int $limit = 5): string
    {
        $filteredTrace = array_slice($trace, 0, $limit);

        $formattedTrace = array_map(function ($item) {
            return isset($item['file'])
                ? basename($item['file']) . ':' . $item['line']
                : '[Unknown file]';
        }, $filteredTrace);

        return implode("\n", $formattedTrace) . (count($trace) > $limit ? "\n...Trace truncated..." : '');
    }
}
