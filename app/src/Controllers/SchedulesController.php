<?php

namespace App\Controllers;

use App\Core\AppSettings;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SchedulesController extends BaseController
{
    /**
     * SchedulesController constructor
     */
    public function __construct() {}

    /**
     * Handles retrieving a list of teams with optional filtering, sorting, and pagination.
     *
     * @param Request $request The incoming HTTP request.
     * @param Response $response The outgoing HTTP response.
     *
     * @return Response JSON response containing team details for the match.
     */
    public function getSchedules(Request $request, Response $response): Response
    {
        //* Validate HTTP Method Sent
        $this->validateHTTPMethod($request);

        $client = new Client();

        try {
            $apiResponse = $client->get('https://api-web.nhle.com/v1/schedule/now');

            $schedules = json_decode($apiResponse->getBody(), true);
        } catch (\Exception $ex) {
            $schedules = ['Error' => 'Schedules are not available!'];
        }
        return $this->renderJson(
            $response,
            [
                "status" => array(
                    "Type" => "successful",
                    "Code" => 200,
                    "Content-Type" => "application/json"
                ),
                "schedules" => $schedules
            ]
        );
    }
}
