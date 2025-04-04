<?php

namespace App\Exceptions;

use Slim\Exception\HttpSpecializedException;

/**
 * Class HttpInvalidMethodException
 *
 * Exception thrown when an invalid HTTP Method is sent as a request.
 *
 */
class HttpInvalidMethodException extends HttpSpecializedException
{
    /**
     * @var int HTTP status code for the exception.
     */
    protected $code = 405;

    /**
     * @var string Default error message.
     */
    protected $message = "Bad Request: Invalid HTTP Method Requested.";

    /**
     * @var string Title for the HTTP response.
     */
    protected string $title = "405 Method Not Allowed";

    /**
     * @var string Detailed description of the error.
     */
    protected string $description = "An Invalid HTTP Method was detected!";
}
