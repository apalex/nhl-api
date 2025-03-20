<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\AppSettings;
use App\Exceptions\HttpNotAcceptableException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

class ContentNegotiationMiddleware implements MiddlewareInterface
{
    public function __construct(private AppSettings $app_settings) {}

    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $acceptHeader = $request->getHeaderLine('Accept');

        if (str_contains($acceptHeader, 'application/json')) {
            return $response;
        }
        if (!str_contains($acceptHeader, 'application/json')) {
            throw new HttpNotAcceptableException($request, "Not Acceptable");
        }
        return $response;
    }
}
