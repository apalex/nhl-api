<?php

namespace App\Exceptions;

use Slim\Exception\HttpSpecializedException;

/**
 * Class HttpInvalidIDException
 *
 * Exception thrown when an invalid ID is provided in a request.
 *
 */
class HttpInvalidIDException extends HttpSpecializedException
{
    /**
     * @var int HTTP status code for the exception.
     */
    protected $code = 400;

    /**
     * @var string Default error message.
     */
    protected $message = "Bad Request: Invalid ID provided.";

    /**
     * @var string Title for the HTTP response.
     */
    protected string $title = "400 Bad Request";

    /**
     * @var string Detailed description of the error.
     */
    protected string $description = "An Invalid ID was detected!";
}
