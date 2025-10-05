<?php
namespace App\Services;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class APIService
{    
    private $apiBaseUrl = 'https://jsonplaceholder.typicode.com/';

    public function fetch(string $url): ?array
    {
        $client = new Client();

        try {
            $response = $client->get($this->apiBaseUrl . $url);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody(), true);
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch data from API: " . $e->getMessage());
        }

        return null;
    }
}