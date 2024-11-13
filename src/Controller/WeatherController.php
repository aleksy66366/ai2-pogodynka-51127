<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    #[Route('/weather/{city}/{country?}/{province?}', name: 'app_weather', requirements: ['city' => '\w+', 'country' => '\w*', 'province' => '\w*'])]
    public function city(
        string $city,
        ?string $country,
        ?string $province,
        LocationRepository $locationRepository,
        WeatherUtil $util,
    ): Response {
        $criteria = ['city' => $city];
        if ($country) {
            $criteria['country'] = $country;
        }
        if ($province) {
            $criteria['province'] = $province;
        }

        $location = $locationRepository->findOneBy($criteria);

        if (!$location) {
            throw $this->createNotFoundException('Location not found for the provided city, country, and province.');
        }

        $measurements = $util->getWeatherForLocation($location);
//        $measurements = $repository->findByLocation($location);

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}
