<?php

namespace App\Services\Carriers;

use GuzzleHttp\Client;

class UpsTrackingService
{
    protected Client $client;
    protected string $baseUrl;
    protected string $accessToken;

    public function __construct()
    {
        $this->client = new Client(['verify' => false]);
        $this->baseUrl = 'https://onlinetools.ups.com'; 
    }

    protected function getAccessToken(): string
    {
        $auth = base64_encode(config('services.ups.client_id') . ':' . config('services.ups.client_secret'));

        $res = $this->client->post("{$this->baseUrl}/security/v1/oauth/token", [
            'headers' => [
                'Authorization' => "Basic {$auth}",
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $data = json_decode($res->getBody(), true);
        $this->accessToken = $data['access_token'] ?? null;

        if (!$this->accessToken) {
            throw new \Exception('UPS token missing');
        }

        return $this->accessToken;
    }

    public function getStatus(string $trackingNumber): ?array
    {
        if (empty($this->accessToken)) {
            $this->getAccessToken();
        }

        $url = "{$this->baseUrl}/track/v1/details/{$trackingNumber}";

        $res = $this->client->get($url, [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Accept'        => 'application/json',
            ],
        ]);

        $data = json_decode($res->getBody(), true);

        if (empty($data['trackResponse']['shipment'][0]['package'][0]['activity'][0])) {
            return null;
        }

        $package = $data['trackResponse']['shipment'][0]['package'][0];
        $latest  = $package['activity'][0] ?? [];

        return [
            'status'             => $latest['status']['type'] ?? 'unknown',
            'status_code'        => $latest['status']['code'] ?? null,
            'status_description' => $latest['status']['description'] ?? null,
            'estimated_delivery' => $package['deliveryDate'][0]['date'] ?? null,
            'last_location'      => $latest['location']['address']['city'] ?? null,
            'delivered_at'       => $latest['date'] ?? null,
            'raw'                => $data,
        ];
    }
}
