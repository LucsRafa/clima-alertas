<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\WeatherLog;
use App\Services\Weather\OpenWeatherService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    /**
     * Return current weather, using cache via weather_logs.
     */
    public function current(City $city, OpenWeatherService $service): JsonResponse
    {
        $freshThreshold = CarbonImmutable::now()->subHour();

        $cached = WeatherLog::query()
            ->where('city_id', $city->id)
            ->where('observed_at', '>=', $freshThreshold)
            ->latest('observed_at')
            ->first();

        if ($cached) {
            return response()->json([
                'cached' => true,
                'data' => $cached->payload,
                'observed_at' => $cached->observed_at,
            ]);
        }

        $payload = $service->currentByCoords((float) $city->lat, (float) $city->lon);

        $log = WeatherLog::create([
            'city_id' => $city->id,
            'payload' => $payload,
            'observed_at' => CarbonImmutable::now(),
        ]);

        return response()->json([
            'cached' => false,
            'data' => $log->payload,
            'observed_at' => $log->observed_at,
        ]);
    }
}

