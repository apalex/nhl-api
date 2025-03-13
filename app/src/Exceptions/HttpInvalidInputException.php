<?php

namespace App\Exceptions;

use Slim\Exception\HttpSpecializedException;

/**
 * Class HttpInvalidInputException
 *
 * Exception thrown when an invalid input is provided in a request.
 *
 */
class HttpInvalidInputException extends HttpSpecializedException
{
    /**
     * @var int HTTP status code for the exception.
     */
    protected $code = 400;

    /**
     * @var string Default error message.
     */
    protected $message = "Bad Request: Invalid Input provided.";

    /**
     * @var string Title for the HTTP response.
     */
    protected string $title = "400 Bad Request";

    /**
     * @var string Detailed description of the error.
     */
    protected string $description = "An Invalid Input was detected!";
}
