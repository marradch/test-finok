<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    const CACHE_STORE_TIME_IN_SEC = 300;

    public function getWeatherInfo(): \stdClass
    {
        if (!env('WEATHER_API_KEY')) {

            throw new \Exception('Not configured');
        }

        $cachedWeather = Cache::get('weather');
        if ($cachedWeather) {
            $cachedWeatherDate = Cache::get('weather_date');
            $cacheTimePass = (int) date('U') - (int) $cachedWeatherDate;
        }

        if ($cachedWeather && $cacheTimePass <= self::CACHE_STORE_TIME_IN_SEC) {

            $weather = json_decode($cachedWeather);
            $weather->source = 'cache';

            return $weather;
        } else {
            $response = Http::get(
                "https://api.openweathermap.org/data/2.5/weather?lat=48.9286&lon=24.7107&appid="
                . env('WEATHER_API_KEY')
            );

            if ($response->status() == 200) {
                $body = json_decode($response->body());

                if ($body && isset($body->main)) {

                    Cache::put('weather', json_encode($body->main));
                    Cache::put('weather_date', date('U'));

                    $weather = $body->main;
                    $weather->source = 'api';

                    return $weather;
                } else {
                    throw new \Exception('Third-party library error.');
                }
            } else {

                throw new \Exception('Third-party library error.');
            }
        }
    }
}
