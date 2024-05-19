<?php
declare(strict_types=1);

namespace Vendor\Weather\Block;

use GuzzleHttp\ClientFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;

class Forecast extends Template
{
    public function __construct(
        private ClientFactory $clientFactory,
        private ScopeConfigInterface $config,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getWeather()
    {
        $client = $this->clientFactory->create();
        $city = $this->config->getValue('general/weather/city');
        $api = $this->config->getValue('general/weather/api_key');
        $uri = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=$api";
        $json = $client->get($uri)->getBody()->getContents();
        $data = json_decode($json, true);
        $icon = $data['weather'][0]['icon'];
        return [
            'city' => $data['name'],
            'temp' => $this->convertKtoC($data['main']['temp']),
            'main' => $data['weather'][0]['main'],
            'description' => $data['weather'][0]['description'],
            'url' => "https://openweathermap.org/img/wn/{$icon}@2x.png",
            'wind' => $data['wind']['speed'],
        ];
    }

    public function getForecast(): array
    {
        $forecast = [];
        $client = $this->clientFactory->create();
        $city = $this->config->getValue('general/weather/city');
        $api = $this->config->getValue('general/weather/api_key');
        $uri = "https://api.openweathermap.org/data/2.5/forecast?q=$city&appid=$api&cnt=5";
        $json = $client->get($uri)->getBody()->getContents();
        $data = json_decode($json, true);
        foreach ($data['list'] as $item) {
            $icon = $item['weather'][0]['icon'];
            $forecast[] = [
                'time' => (new \DateTime($item['dt_txt']))->setTimezone(new \DateTimeZone('Asia/Ho_Chi_Minh'))->format('H\h'),
                'temp_min' => $this->convertKtoC($item['main']['temp_min']),
                'temp_max' => $this->convertKtoC($item['main']['temp_max']),
                'description' => $item['weather'][0]['description'],
                'url' => "https://openweathermap.org/img/wn/{$icon}@2x.png"
            ];
        }
        return $forecast;
    }

    private function convertKtoC($temperatureInKelvin) {
        $temperatureInCelsius = $temperatureInKelvin - 273.15;
        return round($temperatureInCelsius);
    }
}
