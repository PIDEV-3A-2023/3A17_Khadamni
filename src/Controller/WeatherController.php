<?php

namespace App\Controller;

use App\Service\MeteoConceptService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class WeatherController extends AbstractController
{
    public function index(MeteoConceptService $meteoConceptService): Response
    {
        $weather = $meteoConceptService->getWeather('99351');

        return $this->render('weather/index.html.twig', [
            'weather' => $weather,
        ]);
    }
}
