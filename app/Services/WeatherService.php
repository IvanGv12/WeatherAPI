<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;

class WeatherService {

    private $key;

    public function __construct()
    {
        $this->key = config('services.weather.key');
    }

    public function current($city)
    {
        return Http::get('https://api.openweathermap.org/data/2.5/weather', [
            'q' => $city,
            'appid' => $this->key,
            'units' => 'metric',
            'lang' => 'es'
        ])->json();
    }

    public function forecast($city)
    {
        return Http::get('https://api.openweathermap.org/data/2.5/forecast', [
            'q' => $city,
            'appid' => $this->key,
            'units' => 'metric',
            'lang' => 'es'
        ])->json();
    }
}
