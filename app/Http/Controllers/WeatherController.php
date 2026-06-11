<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeocodingService;
use App\Services\WeatherService;

class WeatherController extends Controller
{
    public function weatherByCity(
        Request $request,
        GeocodingService $geocodingService,
        WeatherService $weatherService
    ) {
        $city = $request->query('city');

        if (!$city) {
            return response()->json([
                'error' => 'Kein Ort angegeben'
            ], 400);
        }

        $geoData = $geocodingService->searchCity($city);

        if (!$geoData || count($geoData) === 0) {
            return response()->json([
                'error' => 'Ort wurde nicht gefunden'
            ], 404);
        }

        $location = $geoData[0];

        $weatherData = $weatherService->getWeather(
            $location['lat'],
            $location['lon']
        );

        if (!$weatherData) {
            return response()->json([
                'error' => 'Wetterdaten konnten nicht geladen werden'
            ], 500);
        }

        return response()->json([
            'city' => $location['display_name'],
            'latitude' => $location['lat'],
            'longitude' => $location['lon'],
            'weather' => $weatherData,
        ]);
    }

    public function weatherByLocation(
        Request $request,
        WeatherService $weatherService,
        GeocodingService $geocodingService
    ) {
        $lat = $request->query('lat');
        $lon = $request->query('lon');

        $weatherData = $weatherService->getWeather($lat, $lon);

        $locationData = $geocodingService->reverse($lat, $lon);

        return response()->json([
            'city' => $locationData['display_name'] ?? 'Aktueller Standort',
            'weather' => $weatherData,
        ]);
    }
}