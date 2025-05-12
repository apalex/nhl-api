<?php

namespace App\Controllers;

use App\Models\ArenasModel;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller to provide arena and city weather information.
 */
class CityWeatherController
{
    private ArenasModel $arenasModel;
    private string $weatherApiKey = 'e7e00fe03ed498d402772a4dee37112b';

    public function __construct(ArenasModel $arenasModel)
    {
        $this->arenasModel = $arenasModel;
    }

    /**
     * Fetch arenas and current weather data for their cities.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getCityWeather(Request $request, Response $response): Response
    {
        $arenasResult = $this->arenasModel->getArenas([]);
        $arenas = $arenasResult['data'];
        $client = new Client();
        $result = [];

        foreach ($arenas as $arena) {
            error_log("DEBUG: Arena Data -> " . json_encode($arena));
            $city = $arena['city'] ?? null;
            $weatherData = [];

            if ($city) {
                try {
                    $apiResponse = $client->get('https://api.openweathermap.org/data/2.5/weather', [
                        'query' => [
                            'q' => $city,
                            'appid' => $this->weatherApiKey,
                            'units' => 'metric'
                        ]
                    ]);

                    $weatherJson = json_decode($apiResponse->getBody(), true);

                    $weatherData = [
                        'temperature' => $weatherJson['main']['temp'] . ' °C',
                        'description' => $weatherJson['weather'][0]['description'],
                        'humidity' => $weatherJson['main']['humidity'] . '%'
                    ];
                } catch (\Exception $e) {
                    $weatherData = ['error' => 'Weather data unavailable'];
                }
            } else {
                $weatherData = ['error' => 'City not found in database record'];
            }

            $result[] = [
                'arena' => $arena,
                'weather' => $weatherData
            ];
        }

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
