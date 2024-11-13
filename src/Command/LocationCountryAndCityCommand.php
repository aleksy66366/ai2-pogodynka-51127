<?php

namespace App\Command;

use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'location:countryAndCity',
    description: 'Display location by city name, optional country, province.',
)]
class LocationCountryAndCityCommand extends Command
{
    private WeatherUtil $weatherUtil;

    public function __construct(WeatherUtil $weatherUtil)
    {
        parent::__construct();
        $this->weatherUtil = $weatherUtil;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('city', InputArgument::REQUIRED, 'Name of the city')
            ->addArgument('country', InputArgument::OPTIONAL, 'Country name')
            ->addArgument('province', InputArgument::OPTIONAL, 'Province name');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $city = $input->getArgument('city');
        $country = $input->getArgument('country') ?? '';  // Opcjonalne
        $province = $input->getArgument('province') ?? ''; // Opcjonalne

        $io->writeln('Searching with criteria:');
        $io->writeln(sprintf("City: %s, Country: %s, Province: %s", $city, $country ?: 'N/A', $province ?: 'N/A'));


        $measurements = $this->weatherUtil->getWeatherForCountryAndCity($country, $city, $province);

        if (empty($measurements)) {
            $io->error('No weather data found for the provided city, country, and province.');
            return Command::FAILURE;
        }

        $location = $measurements[0]->getLocation();
        $io->writeln(sprintf('Weather forecast for: %s, %s, %s',
            $location->getCity(),
            $location->getCountry(),
            $location->getProvince() ?? 'N/A'
        ));

        foreach ($measurements as $measurement) {
            $io->writeln(sprintf(
                "\t%s: %s°C",
                $measurement->getDate()->format('Y-m-d'),
                $measurement->getCelsius()
            ));
        }

        return Command::SUCCESS;
    }
}
