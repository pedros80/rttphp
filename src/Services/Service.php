<?php

namespace Pedros80\RTTphp\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Pedros80\RTTphp\Exceptions\InvalidDateFormat;
use Pedros80\RTTphp\Exceptions\InvalidTimeFormat;
use Pedros80\RTTphp\Exceptions\ServiceNotFound;

abstract class Service
{
    public function __construct(
        protected Client $client
    ) {
    }

    protected function get(string $url): array
    {
        try {
            $response = $this->client->get($url);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw ServiceNotFound::fromUrl($url);
            }
        }

        return [];
    }

    protected function isDateFormatValid(string $date): bool
    {
        if (!preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $date)) {
            throw InvalidDateFormat::fromString($date);
        }

        return true;
    }

    protected function isTimeFormatValid(string $time): bool
    {
        if (!preg_match('/^([01][0-9]|2[0-3])[0-5][0-9]$/', $time)) {
            throw InvalidTimeFormat::fromString($time);
        }

        return true;
    }
}
