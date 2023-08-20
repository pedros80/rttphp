<?php

namespace Tests\Unit\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Pedros80\RTTphp\Exceptions\InvalidDateFormat;
use Pedros80\RTTphp\Exceptions\InvalidTimeFormat;
use Pedros80\RTTphp\Exceptions\ServiceNotFound;
use Pedros80\RTTphp\Services\LocationService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class LocationServiceTest extends TestCase
{
    use ProphecyTrait;

    public function testSearchStationHitsCorrectEndpoint(): void
    {
        $client = $this->prophesize(Client::class);
        $client->get('json/search/KDY')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));
        $service = new LocationService($client->reveal());
        $service->search('KDY');
    }

    public function testSearchStationWithToStationHitsCorrectEndpoint(): void
    {
        $client = $this->prophesize(Client::class);
        $client->get('json/search/KDY/to/DAM')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));
        $service = new LocationService($client->reveal());
        $service->search('KDY', 'DAM');
    }

    public function testSearchStationWithInvalidDateThrowsException(): void
    {
        $this->expectException(InvalidDateFormat::class);
        $this->expectExceptionMessage("'2023-80-99' is not a valid date - yyyy/mm/dd");

        $service = new LocationService($this->prophesize(Client::class)->reveal());
        $service->search(
            station: 'KDY',
            date: '2023-80-99'
        );
    }

    public function testSearchStationWithValidDateAndInvalidTimeThrowsEzception(): void
    {
        $this->expectException(InvalidTimeFormat::class);
        $this->expectExceptionMessage("'9999' is not a valid time - hhmm");

        $service = new LocationService($this->prophesize(Client::class)->reveal());
        $service->search(
            station: 'KDY',
            date: '2023/08/19',
            time: '9999'
        );
    }

    public function testSearchStationWithValidDateAndNoTimeHitsCorrectEndpoint(): void
    {
        $client = $this->prophesize(Client::class);
        $client->get('json/search/KDY/2023/08/19')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));
        $service = new LocationService($client->reveal());
        $service->search(
            station: 'KDY',
            date: '2023/08/19',
        );
    }

    public function testSearchStationWithValidDateAndTimeHitsCorrectEndpoint(): void
    {
        $client = $this->prophesize(Client::class);
        $client->get('json/search/KDY/2023/08/19/2345')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));
        $service = new LocationService($client->reveal());
        $service->search(
            station: 'KDY',
            date: '2023/08/19',
            time: '2345'
        );
    }

    public function testSearchStationWithTimeAndNoDateIsIgnored(): void
    {
        $client = $this->prophesize(Client::class);
        $client->get('json/search/KDY')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));
        $service = new LocationService($client->reveal());
        $service->search(
            station: 'KDY',
            time: '9999'
        );
    }

    public function testSearchStationToStationWithValidDateAndNoTimeHitsCorrectEndpoint(): void
    {
        $client = $this->prophesize(Client::class);
        $client->get('json/search/KDY/to/DAM/2023/08/19')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));
        $service = new LocationService($client->reveal());
        $service->search(
            station: 'KDY',
            toStation: 'DAM',
            date: '2023/08/19',
        );
    }

    public function testSearchStationToStationWithValidDateAndTimeHitsCorrectEndpoint(): void
    {
        $client = $this->prophesize(Client::class);
        $client->get('json/search/KDY/to/DAM/2023/08/19/2345')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));
        $service = new LocationService($client->reveal());
        $service->search(
            station: 'KDY',
            toStation: 'DAM',
            date: '2023/08/19',
            time: '2345'
        );
    }

    public function testSearchStationToStationWithTimeAndNoDateIsIgnored(): void
    {
        $client = $this->prophesize(Client::class);
        $client->get('json/search/KDY/to/DAM')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));
        $service = new LocationService($client->reveal());
        $service->search(
            station: 'KDY',
            time: '9999',
            toStation: 'DAM'
        );
    }

    public function testSearchUnknownStationThrowsException(): void
    {
        $this->expectException(ServiceNotFound::class);
        $this->expectExceptionMessage("Could not find service from 'json/search/XXXXXX'. Please check url.");

        $client = $this->prophesize(Client::class);
        $client->get('json/search/XXXXXX')->shouldBeCalled()->willReturn(new Response(404, [], '{}'));
        $service = new LocationService($client->reveal());
        $service->search(
            station: 'XXXXXX',
        );
    }

    public function testSearchThirdPartyErrorThrowsException(): void
    {
        $this->expectException(ServiceNotFound::class);
        $this->expectExceptionMessage("Could not find service from 'json/search/XXXXXX'. Please check url.");

        $client = $this->prophesize(Client::class);
        $client->get('json/search/XXXXXX')->shouldBeCalled()->willReturn(
            new Response(200, [], '{"error":"Unknown error"}')
        );
        $service = new LocationService($client->reveal());
        $service->search(
            station: 'XXXXXX',
        );
    }

    public function testUnknownErrorThrowsException(): void
    {
        $this->expectException(Exception::class);

        $client = $this->prophesize(Client::class);
        $client->get('json/search/KDY')->shouldBeCalled()->willThrow(new Exception('error', 500));
        $service = new LocationService($client->reveal());
        $service->search(station: 'KDY');
    }
}
