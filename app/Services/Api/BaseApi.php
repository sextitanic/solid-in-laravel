<?php

namespace App\Services\Api;

use GuzzleHttp\Client;

abstract class BaseApi
{
    protected $client;
    protected $host;

    public function __construct()
    {
        $this->client = new Client();
    }

    private function curl(string $method, string $url, array $fields = [])
    {
        try {
            $response = '';

            $url = $this->host . $url;

            switch ($method) {
                case 'GET':
                    $response = $this->client->request('GET', $url);
                    break;
                case 'POST':
                    $response = $this->client->request('POST', $url, ['form_params' => $fields]);
                    break;
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new \App\Exceptions\ApiException($e->getMessage());
        }
    }

    public function get(string $url)
    {
        return $this->curl('GET', $url);
    }

    public function post(string $url, array $fields)
    {
        return $this->curl('POST', $url, $fields);
    }
}
