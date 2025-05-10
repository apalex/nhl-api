<?php

/**
 * Middleware configuration file for the Slim application.
 *
 * This file defines and adds middleware layers such as content negotiation,
 * body parsing, routing, and error handling.
 *
 * @package App\Middleware
 */

declare(strict_types=1);

use App\Handlers\CustomErrorHandler;
use App\Middleware\AccessLogMiddleware;
use App\Middleware\ContentNegotiationMiddleware;
use Slim\App;
use App\Middleware\JwtMiddleware;


/**
 * Middleware registration function.
 *
 * @param App $app The Slim application instance.
 *
 * @return void
 */
return function (App $app): void {
    /**
     * Adds Content Negotiation Middleware to handle accepted content types.
     */
    $app->add(ContentNegotiationMiddleware::class);

    /**
     * Adds JWT Authentication & Authorization middleware:
     * - Skips /login and /register
     * - Allows GET for any authenticated user
     * - Restricts POST/PUT/DELETE to admins only
     */
    $app->add(new JwtMiddleware('1234'));

    /**
     * Logs all incoming HTTP requests (method, URI, IP, query parameters).
     */
    $app->add(AccessLogMiddleware::class);

    /**
     * Adds Body Parsing Middleware to parse incoming request bodies.
     */
    $app->addBodyParsingMiddleware();

    /**
     * Adds Routing Middleware to enable routing features.
     */
    $app->addRoutingMiddleware();

    /**
     * Adds Error Handling Middleware, which must be added last.
     * This ensures proper JSON error responses.
     *
     * @var \Slim\Middleware\ErrorMiddleware $errorMiddleware
     */
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);

    /**
     * Sets the error handler to a Custom Error Handler for JSON formatting.
     */
    $errorMiddleware->setDefaultErrorHandler(CustomErrorHandler::class);
};
