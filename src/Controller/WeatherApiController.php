<?php

namespace App\Controller;

use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

class WeatherApiController extends AbstractController
{
    private WeatherUtil $weatherUtil;

    public function __construct(WeatherUtil $weatherUtil)
    {
        $this->weatherUtil = $weatherUtil;
    }

    #[Route('/api/v1/weather', name: 'app_weather_api', methods: ['GET'])]
    public function index(
        #[MapQueryParameter] string $country = null,
        #[MapQueryParameter] string $city = null,
        #[MapQueryParameter] string $format = 'json',
        #[MapQueryParameter('twig')] bool $twig = false
    ): Response {
        $measurements = $this->weatherUtil->getWeatherForCountryAndCity($country, $city);

        if ($twig) {
            if ($format === 'csv') {
                $csvContent = $this->renderView('weather_api/index.csv.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);

                $csvContent = str_replace(["\r\n", "\r", "\n"], "\n", $csvContent);

                return new Response($csvContent, 200, [
                    'Content-Type' => 'text/plain; charset=UTF-8',
                ]);
            } else {
                return $this->render('weather_api/index.json.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);
            }
        }

        if ($format === 'csv') {
            $csvData = array_map(function($m) use ($city, $country) {
                return sprintf('%s,%s,%s,%s',
                    $city,
                    $country,
                    $m->getDate()->format('Y-m-d'),
                    $m->getCelsius()
                );
            }, $measurements);
            array_unshift($csvData, 'city,country,date,celsius');
            $csvContent = implode("\n", $csvData);
            return new Response($csvContent, 200, [
                'Content-Type' => 'text/plain',
            ]);
        }

        $formattedMeasurements = array_map(fn($m) => [
            'date' => $m->getDate()->format('Y-m-d'),
            'celsius' => $m->getCelsius(),
        ], $measurements);

        return $this->json([
            'country' => $country,
            'city' => $city,
            'measurements' => $formattedMeasurements,
        ]);
    }
}
