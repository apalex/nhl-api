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

use App\Middleware\ContentNegotiationMiddleware;
use Slim\App;

/**
 * Middleware registration function.
 *
 * @param App $app The Slim application instance.
 *
 * @return void
 */
return function (App $app): void {
    // TODO: Add your middleware here.

    /**
     * Adds Content Negotiation Middleware to handle accepted content types.
     */
    $app->add(ContentNegotiationMiddleware::class);

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
     * This ensures proper error handling behavior in the application.
     *
     * @var \Slim\Middleware\ErrorMiddleware $errorMiddleware
     */
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);

    /**
     * Forces the default error handler to return JSON-formatted error responses.
     */
    $errorMiddleware->getDefaultErrorHandler()->forceContentType(APP_MEDIA_TYPE_JSON);

    //! NOTE: You can override the default error handler with your custom error handler.
    //* For more details, refer to Slim framework's documentation.
};
