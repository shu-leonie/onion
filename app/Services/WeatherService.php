<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class WeatherService
{
    public function getWeather($latitude, $longitude)
    {   
        try {
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'hourly' => implode(',', [
                    'temperature_2m',
                    'apparent_temperature',
                    'rain',
                    'cloud_cover',
                    'uv_index',
                    'is_day',
                    'weather_code'
                ]),
                'timezone' => 'Europe/Berlin',
            ]);

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            return [
                'time' => $data['hourly']['time'],
                'apparentTemperature' => $data['hourly']['apparent_temperature'],
                'rain' => $data['hourly']['rain'],
                'cloudCover' => $data['hourly']['cloud_cover'],
                'uvIndex' => $data['hourly']['uv_index'],
                'isDay' => $data['hourly']['is_day'],
                'weather' => mapWeatherCodes($data['hourly']['weather_code']),
            ];
        } catch (ConnectionException $e) {
            // Timeout / DNS / SSL / Netzwerkfehler
            return null;
        }

    }
}


/* WEATHER CODES
Code	Description
0	Clear sky
1, 2, 3	Mainly clear, partly cloudy, and overcast
45, 48	Fog and depositing rime fog
51, 53, 55	Drizzle: Light, moderate, and dense intensity
56, 57	Freezing Drizzle: Light and dense intensity
61, 63, 65	Rain: Slight, moderate and heavy intensity
66, 67	Freezing Rain: Light and heavy intensity
71, 73, 75	Snow fall: Slight, moderate, and heavy intensity
77	Snow grains
80, 81, 82	Rain showers: Slight, moderate, and violent
85, 86	Snow showers slight and heavy
95 *	Thunderstorm: Slight or moderate
96, 99 *	Thunderstorm with slight and heavy hail
*/

function mapWeatherCodes(array $weatherCodes): array
{
    $weatherMap = [
        0 => [
            'description' => 'Klarer Himmel',
            'image' => 'sunny.png',
        ],

        1 => [
            'description' => 'Überwiegend klar',
            'image' => 'mostly_sunny.png',
        ],

        2 => [
            'description' => 'Teilweise bewölkt',
            'image' => 'mostly_cloudy_day.png',
        ],

        3 => [
            'description' => 'Bedeckt',
            'image' => 'cloudy.png',
        ],

        45 => [
            'description' => 'Nebel',
            'image' => 'cloudy.png', // kein eigenes Fog-Bild vorhanden
        ],

        48 => [
            'description' => 'Raureif-Nebel',
            'image' => 'cloudy.png',
        ],

        51 => [
            'description' => 'Leichter Nieselregen',
            'image' => 'rain_light.png',
        ],

        53 => [
            'description' => 'Mäßiger Nieselregen',
            'image' => 'rain.png',
        ],

        55 => [
            'description' => 'Starker Nieselregen',
            'image' => 'rain_heavy.png',
        ],

        61 => [
            'description' => 'Leichter Regen',
            'image' => 'rain_light.png',
        ],

        63 => [
            'description' => 'Mäßiger Regen',
            'image' => 'rain.png',
        ],

        65 => [
            'description' => 'Starker Regen',
            'image' => 'rain_heavy.png',
        ],

        71 => [
            'description' => 'Leichter Schneefall',
            'image' => 'snow_light.png',
        ],

        73 => [
            'description' => 'Mäßiger Schneefall',
            'image' => 'snow.png',
        ],

        75 => [
            'description' => 'Starker Schneefall',
            'image' => 'snow_heavy.png',
        ],

        77 => [
            'description' => 'Schneegriesel',
            'image' => 'snow.png',
        ],

        80 => [
            'description' => 'Regenschauer',
            'image' => 'rain.png',
        ],

        81 => [
            'description' => 'Starke Regenschauer',
            'image' => 'rain_heavy.png',
        ],

        82 => [
            'description' => 'Sehr starke Regenschauer',
            'image' => 'rain_heavy.png',
        ],

        85 => [
            'description' => 'Schneeschauer',
            'image' => 'snow_s_cloudy.png',
        ],

        86 => [
            'description' => 'Starke Schneeschauer',
            'image' => 'snow_heavy.png',
        ],

        95 => [
            'description' => 'Gewitter',
            'image' => 'thunderstorms.png',
        ],

        96 => [
            'description' => 'Gewitter mit Hagel',
            'image' => 'thunderstorms.png',
        ],

        99 => [
            'description' => 'Gewitter mit starkem Hagel',
            'image' => 'thunderstorms.png',
        ],
    ];

    return array_map(function ($code) use ($weatherMap) {
        return [
            'code' => $code,
            'description' => $weatherMap[$code]['description'] ?? 'Unbekannt',
            'image' => $weatherMap[$code]['image'] ?? 'unknown.png',
        ];
    }, $weatherCodes);
}