<?php

namespace Pedros80\RTTphp\Factories;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Pedros80\RTTphp\Services\LocationService;
use Pedros80\RTTphp\Services\ServiceInformationService;

final class ServicesFactory
{
    private const BASE_URI   = 'https://api.rtt.io/api/v1/';
    private const USER_AGENT = 'RTTphp';
    private const TIMEOUT    = 20;

    private function makeClient(string $user, string $pass): Client
    {
        $auth = base64_encode("{$user}:{$pass}");

        return new Client([
            'base_uri'               => self::BASE_URI,
            RequestOptions::HEADERS  => [
                'User-Agent'    => self::USER_AGENT,
                'Authorization' => "Basic {$auth}",
                'Content-Type'  => 'application/json',
            ],
            RequestOptions::TIMEOUT => self::TIMEOUT,
        ]);
    }

    public function makeLocationService(string $user, string $pass): LocationService
    {
        return new LocationService($this->makeClient($user, $pass));
    }

    public function makeServiceInformationService(string $user, string $pass): ServiceInformationService
    {
        return new ServiceInformationService($this->makeClient($user, $pass));
    }
}
