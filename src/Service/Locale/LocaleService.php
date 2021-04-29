<?php


namespace App\Service\Locale;

use App\Entity\Address;
use App\Repository\CountryRepository;

class LocaleService
{
    protected $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * Return the distance of the address' city (if in nigeria) form Ilobu in km or null
     *
     * @param Address $address
     * @return float|null
     */
    public function getDistanceFromIlobuInKm(Address $address, string $distanceUnit = 'km', int $decimalPoints = 2, $round = true): ?float
    {
        $ngCities = LocaleService::getArrayNgCities();
        $ilobu = $ngCities[276];
        $ilobuLat = $ilobu['lat'];
        $ilobuLng = $ilobu['lng'];

        $cityItem = $address->getCityItem();
        $lat = $cityItem['lat'];
        $lng = $cityItem['lng'];

        $distanceUnit = strtolower($distanceUnit);
        $pointDifference = $lng - $ilobuLng;
        $toSin = (sin(deg2rad($lat)) * sin(deg2rad($ilobuLat))) + (cos(deg2rad($lat)) * cos(deg2rad($ilobuLat)) * cos(deg2rad($pointDifference)));
        $toAcos = acos($toSin);
        $toRad2Deg = rad2deg($toAcos);

        $toMiles  =  $toRad2Deg * 60 * 1.1515;
        $toKilometers = $toMiles * 1.609344;

        switch (strtoupper($distanceUnit)) {
            case 'ML': //miles
                $toMiles  = ($round == true ? round($toMiles) : round($toMiles, $decimalPoints));
                return $toMiles;
                break;
            case 'KM': //Kilometers
                $toKilometers  = ($round == true ? round($toKilometers) : round($toKilometers, $decimalPoints));
                return $toKilometers;
                break;
            default:
                return $toKilometers;
                break;
        }
    }


    /**
     * Undocumented function
     *
     * @param in
     * @param boolean $native
     * @return ?|string
     */
    public static function getCountryCodeByCityId(int $cityId): ?string
    {
        $cities = self::getArrayCities();
        return $cities[$cityId]['country'];
    }



    public static function getCountryChoices(bool $native = false): array
    {
        $a = self::getCountryConst($native);
        asort($a);
        return $a;
    }

    public static function getCountryConst(bool $native = false): array
    {
        $array = self::getArrayCountries();
        $countries = [];
        if ($native === true) {
            foreach ($array as $k => $v) {
                $countries[$k] = $v['native'];
            }   
        } 
        else {
            foreach ($array as $k => $v) {
                $countries[$k] = $v['name'];
            }
        }

        return $countries;
    }



    public static function getNgStateChoices(): array
    {
        $a = self::reverseKV(self::getNgStateConst());
        ksort($a);
        return $a;
    }

    public static function getNgCityChoices(): array
    {
        $a = self::reverseKV(self::getNgCityConst());
        ksort($a);
        return $a;
    }

    public static function getNgCityConst(): array
    {
        $a = self::getArrayNgCities();
        $cities = [];

        foreach ($a as $k => $v) {
            $cities[$k] = $v['city'];
        }
        return $cities;
    }


    public static function getCityChoices(string $countryCode): array
    {
        $a = self::getCitiesListByCountryCode($countryCode);

        $cities = [];

        foreach ($a as $k => $v) {
            $cities[$v['name']] = $k;
        }
        ksort($cities);
        return  $cities;
    }

    /**
     * Undocumented function
     *
     * @param string $country
     * @return array
     */
    public static function getCitiesListByCountryCode(string $countryCode): array
    {
        return  array_filter(self::getArrayCities(), function ($v) use ($countryCode) {
            return $v['country'] == $countryCode;
        });
    }


    /**
     * @return array
     */
    public static function getArrayCountries(): array
    {
        $json = file_get_contents('%kernel.project_dir%/../../public/lib/countries.json');
        return  json_decode($json, true);
    }

    /**
     * @return array
     */
    public static function getArrayCities(): array
    {
        $json = file_get_contents('%kernel.project_dir%/../../public/lib/cities.json');
        return json_decode($json, true);
    }

    /**
     * @return array
     */
    public static function getNgStateConst(): array
    {
        $json = file_get_contents('%kernel.project_dir%/../../public/lib/ngStates.json');
        return json_decode($json, true);
    }

    /**
     * @return array
     */
    public static function getArrayNgCities(): array
    {
        $json = file_get_contents('%kernel.project_dir%/../../public/lib/ng.json');
        return json_decode($json, true);
    }

    /**
     * @return array
     */
    public static function getContinentConst(): array
    {
        $json = file_get_contents('%kernel.project_dir%/../../public/lib/continents.json');
        return json_decode($json, true);
    }



    private static function reverseKV(array $a): array
    {
        $choices = [];
        foreach ($a as $k => $v) {
            $choices[$v] = $k;
        }
        return $choices;
    }
}
