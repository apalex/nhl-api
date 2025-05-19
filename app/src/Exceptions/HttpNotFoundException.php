<?php

namespace App\Exceptions;

use Slim\Exception\HttpSpecializedException;

/**
 * Class HttpNotFoundException
 *
 * Exception thrown when an invalid ID is provided in a request.
 *
 */
class HttpNotFoundException extends HttpSpecializedException
{
    /**
     * @var int HTTP status code for the exception.
     */
    protected $code = 404;

    /**
     * @var string Default error message.
     */
    protected $message = "Not Found: There are no records found in the database!";

    /**
     * @var string Title for the HTTP response.
     */
    protected string $title = "404 Not Found";

    /**
     * @var string Detailed description of the error.
     */
    protected string $description = "No records are found!";
}
