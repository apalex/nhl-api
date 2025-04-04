<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\HttpInvalidMethodException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

abstract class BaseController
{

    public function __construct() {}
    protected function renderJson(Response $response, array $data, int $status_code = 200): Response
    {

        // var_dump($data);
        $payload = json_encode($data, JSON_UNESCAPED_SLASHES |    JSON_PARTIAL_OUTPUT_ON_ERROR);
        //-- Write JSON data into the response's body.
        $response->getBody()->write($payload);
        return $response->withStatus($status_code)->withAddedHeader(HEADERS_CONTENT_TYPE, APP_MEDIA_TYPE_JSON);
    }

    /**
     * Validates HTTP Method
     *
     * @param Request $request The incoming HTTP method request.
     *
     * @throws HttpInvalidMethodException If the HTTP method is not allowed.
     */
    protected function validateHTTPMethod($request, array $allowedMethods = ["GET"])
    {
        $method = $request->getMethod();

        if (!in_array($method, $allowedMethods)) {
            throw new HttpInvalidMethodException($request, "Invalid Method! Allowed methods: " . implode(", ", $allowedMethods));
        }
    }
}
