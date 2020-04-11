<?php


namespace App\Service;


use GuzzleHttp\Client;
use JMS\Serializer\Serializer;

class Weather
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $apiKey;


    /**
     * Weather constructor.
     * @param Client $client
     * @param Serializer $serializer
     * @param string $apiKey
     */
    public function __construct(Client $client, Serializer $serializer, string $apiKey)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->apiKey = $apiKey;
    }

    public function getCurrent()
    {
        $uri = '/data/2.5/weather?q=Paris&APPID=';
        $result = $response = $this->client->get($uri.$this->apiKey);
        $result = $response->getBody()->getContents();

        $data = $this->serializer->deserialize($result, 'array', 'json');

        return [
            "City" => $data['name'],
            "Description" => $data['weather'][0]['main']
        ];
    }
}