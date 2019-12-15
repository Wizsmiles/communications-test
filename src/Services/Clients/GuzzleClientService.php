<?php


namespace App\Services\Clients;

use GuzzleHttp\Client;

class GuzzleClientService implements HttpClientInterface
{

    public function request($method, $uri, $options = [])
    {
        $client = new Client();
        $response = $client->request($method, $uri, $options);
        return $response->getBody()->getContents();
    }

}