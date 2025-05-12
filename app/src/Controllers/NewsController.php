<?php

namespace App\Controllers;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller for fetching NHL news from external NewsAPI.
 */
class NewsController extends BaseController
{
    /**
     * Your NewsAPI key.
     * Sign up at https://newsapi.org/ to get one (free tier available).
     */
    private string $newsApiKey = 'f556ad500d1c478092f95f1de1deebc0';

    public function __construct(){}
    /**
     * Handles GET /composite/nhl-news
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getNHLNews(Request $request, Response $response): Response
    {
        $client = new Client();
        $newsData = [];

        try {
            $apiResponse = $client->get('https://newsapi.org/v2/everything', [
                'query' => [
                    'q'         => 'NHL',
                    'apiKey'    => $this->newsApiKey,
                    'language'  => 'en',
                    'pageSize'  => 5
                    ]
            ]);

            $newsJson = json_decode($apiResponse->getBody(), true);
            $articles = $newsJson['articles'] ?? [];

            $newsData = array_map(function ($article) {
                return [
                    'title'         => $article['title'],
                    'source'        => $article['source']['name'],
                    'url'           => $article['url'],
                    'publishedAt'   => $article['publishedAt']
                ];
            }, $articles);

        } catch (\Exception $ex) {
            $newsData = ['Error' => 'News is not available!'];
        }

        $response->getBody()->write(json_encode(['news' => $newsData]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
