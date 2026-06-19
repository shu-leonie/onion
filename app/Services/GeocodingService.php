<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GeocodingService
{
    public function reverse($lat, $lon)
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'OnionApp/1.0',
            ])->get('https://nominatim.openstreetmap.org/reverse', [
                'lat' => $lat,
                'lon' => $lon,
                'format' => 'json',
            ]);

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        } catch (ConnectionException $e) {
            // Timeout / DNS / SSL / Netzwerkfehler
            return null;
        }

    }

    public function searchCity($city)
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'OnionApp/1.0',
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $city,
                'format' => 'json',
                'limit' => 1,
            ]);

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        } catch (ConnectionException $e) {
            // Timeout / DNS / SSL / Netzwerkfehler
            return null;
        }

    }
}
