<?php

namespace App\Service;

use GuzzleHttp\Client;

class MeteoConceptService
{
    private $client;
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.meteo-concept.com/api/',
        ]);
        $this->apiKey = $apiKey;
    }

    public function getWeather(string $city): array
    {
        $response = $this->client->request('GET', 'forecast/daily', [
            'query' => [
                'token' => $this->apiKey,
                'insee' => $city,
                'unit' => 'c',
            ],
        ]);
        $data = json_decode($response->getBody(), true);

        return [
            'temperature' => $data['forecast'][0]['tmax'],
            'description' => $data['forecast'][0]['weather'],
            'icon' => sprintf('https://static.meteo-concept.com/icons/%s.png', $data['forecast'][0]['weather_icon']),
        ];
    }
}
