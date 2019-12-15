<?php


namespace App\Services\Clients;


interface HttpClientInterface
{
    public function request($method, $uri, $options = null);
}