<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalWeatherService
{
    /**
     * Map WMO weather code to readable string.
     */
    protected array $wmoCodes = [
        0 => 'Clear Sky',
        1 => 'Mainly Clear',
        2 => 'Partly Cloudy',
        3 => 'Overcast',
        45 => 'Foggy',
        48 => 'Depositing Rime Fog',
        51 => 'Light Drizzle',
        53 => 'Moderate Drizzle',
        55 => 'Dense Drizzle',
        61 => 'Slight Rain',
        63 => 'Moderate Rain',
        65 => 'Heavy Rain',
        71 => 'Slight Snow',
        73 => 'Moderate Snow',
        75 => 'Heavy Snow',
        80 => 'Slight Rain Showers',
        81 => 'Moderate Rain Showers',
        82 => 'Violent Rain Showers',
        95 => 'Thunderstorm',
        96 => 'Thunderstorm with Slight Hail',
        99 => 'Thunderstorm with Heavy Hail',
    ];

    /**
     * Fetch current weather for given latitude and longitude.
     * Handled with short timeout and robust fallbacks.
     */
    public function getWeather(float $latitude, float $longitude): array
    {
        try {
            $response = Http::timeout(3)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => round($latitude, 4),
                'longitude' => round($longitude, 4),
                'current' => 'temperature_2m,weather_code,wind_speed_10m',
                'timezone' => 'auto',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $current = $data['current'] ?? [];
                $weatherCode = $current['weather_code'] ?? 0;
                $temperature = $current['temperature_2m'] ?? 24.0;
                $windSpeed = $current['wind_speed_10m'] ?? 10.0;

                return [
                    'temperature' => (float) $temperature,
                    'summary' => $this->wmoCodes[$weatherCode] ?? 'Partly Cloudy',
                    'wind_speed' => (float) $windSpeed,
                    'is_live' => true,
                ];
            }
        } catch (\Throwable $e) {
            Log::info("External weather API unavailable: " . $e->getMessage());
        }

        // Deterministic realistic fallback based on latitude (e.g. tropical vs temperate)
        $simulatedTemp = abs($latitude) < 20 ? 29.5 : (abs($latitude) < 45 ? 20.0 : 12.0);
        return [
            'temperature' => $simulatedTemp,
            'summary' => 'Fair Weather (Port Baseline)',
            'wind_speed' => 12.5,
            'is_live' => false,
        ];
    }
}
