<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Location;
use App\Entity\Measurement;
use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;

class WeatherUtil
{
    private MeasurementRepository $measurementRepository;
    private LocationRepository $locationRepository;

    public function __construct(
        MeasurementRepository $measurementRepository,
        LocationRepository $locationRepository
    ) {
        $this->measurementRepository = $measurementRepository;
        $this->locationRepository = $locationRepository;
    }
    /**
     * @return Measurement[]
     */
    public function getWeatherForLocation(Location $location): array
    {

        return $this->measurementRepository->findByLocation($location);
    }

    /**
     * @return Measurement[]
     */
    public function getWeatherForCountryAndCity(string $country, string $city, ?string $province = null): array
    {
        $criteria = ['city' => $city];

        if ($country) {
            $criteria['country'] = $country;
        }
        if ($province) {
            $criteria['province'] = $province;
        }


        $location = $this->locationRepository->findOneBy($criteria);


        if (!$location) {
            var_dump('Location not found');
            return [];
        }

        return $this->getWeatherForLocation($location);
    }
}
