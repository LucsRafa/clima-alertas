<?php

namespace App\Services\Weather;

use Illuminate\Support\Facades\Http;

class OpenWeatherService
{
    /**
     * Fetch current weather by coordinates (lat, lon).
     * Returns provider JSON decoded as array.
     */
    public function currentByCoords(float $lat, float $lon): array
    {
        $key = (string) config('services.openweather.key');
        $base = (string) config('services.openweather.base_url', 'https://api.openweathermap.org/data/2.5');

        $resp = Http::retry(3, 300)->get(rtrim($base, '/').'/weather', [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $key,
            'units' => 'metric',
        ])->throw();

        return $resp->json();
    }
}

