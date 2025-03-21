<?php

/**
 * Namespace declaration for the App\Exceptions namespace.
 */
namespace App\Exceptions;

use Slim\Exception\HttpSpecializedException;

/**
 * Class HttpNotAcceptableException
 *
 * Exception thrown when the request is not acceptable (HTTP status code 406).
 * This occurs when the client requests a content type other than JSON.
 *
 * @package App\Exceptions
 */
class HttpNotAcceptableException extends HttpSpecializedException
{
    /**
     * @var int The HTTP status code for "Not Acceptable".
     */
    protected $code = 406;

    /**
     * @var string Default error message for "Not Acceptable".
     */
    protected $message = 'Not Acceptable';

    /**
     * @var string Title for the exception, shown in error details.
     */
    protected string $title = '406 Not Acceptable';

    /**
     * @var string Detailed description for the exception.
     */
    protected string $description = 'Request is Not Acceptable, Values other than /json is not Acceptable!';
}
