<?php

/**
 * Middleware for Content Negotiation.
 *
 * Ensures that the incoming request has an acceptable content type,
 * specifically 'application/json'. Throws an exception if the content type is unacceptable.
 *
 * @package App\Middleware
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\AppSettings;
use App\Exceptions\HttpNotAcceptableException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Class ContentNegotiationMiddleware
 *
 * Middleware that verifies the 'Accept' header of incoming requests.
 *
 * @package App\Middleware
 */
class ContentNegotiationMiddleware implements MiddlewareInterface
{
    /**
     * @var AppSettings Application settings instance.
     */
    public function __construct(private AppSettings $app_settings) {}

    /**
     * Processes the request and ensures it accepts 'application/json'.
     *
     * @param Request $request Incoming server request.
     * @param RequestHandler $handler Request handler to continue the pipeline.
     *
     * @throws HttpNotAcceptableException If the 'Accept' header is missing or invalid.
     *
     * @return ResponseInterface The processed response if the content type is acceptable.
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        /**
         * Extracts the 'Accept' header from the incoming request.
         */
        $acceptHeader = $request->getHeaderLine('Accept');

        /**
         * Ensures the 'Accept' header includes 'application/json'.
         */
        if (str_contains($acceptHeader, 'application/json')) {
            return $response;
        }

        /**
         * Throws an exception if the 'Accept' header does not include 'application/json'.
         */
        if (!str_contains($acceptHeader, 'application/json')) {
            throw new HttpNotAcceptableException($request, "Not Acceptable");
        }

        return $response;
    }
}