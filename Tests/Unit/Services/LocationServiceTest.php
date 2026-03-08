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
use Prophecy\Prophecy\ObjectProphecy;

final class LocationServiceTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<Client> $client */
    private ObjectProphecy $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->prophesize(Client::class);
    }

    public function testSearchStationHitsCorrectEndpoint(): void
    {
        $this->client->get('json/search/KDY')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));

        $service = $this->makeLocationService();
        $service->search('KDY');
    }

    public function testSearchStationArrivalsHitsCorrectEndpoint(): void
    {
        $this->client->get('json/search/KDY/arrivals')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));

        $service = $this->makeLocationService();
        $service->search(station: 'KDY', arrivals: true);
    }

    public function testSearchStationWithToStationHitsCorrectEndpoint(): void
    {
        $this->client->get('json/search/KDY/to/DAM')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));

        $service = $this->makeLocationService();
        $service->search('KDY', 'DAM');
    }

    public function testSearchStationWithToStationArrivalsHitsCorrectEndpoint(): void
    {
        $this->client->get('json/search/KDY/to/DAM/arrivals')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));

        $service = $this->makeLocationService();
        $service->search(station: 'KDY', toStation: 'DAM', arrivals: true);
    }

    public function testSearchStationWithInvalidDateThrowsException(): void
    {
        $this->expectException(InvalidDateFormat::class);
        $this->expectExceptionMessage("'2023-80-99' is not a valid date - yyyy/mm/dd");

        $service = $this->makeLocationService();
        $service->search(
            station: 'KDY',
            date: '2023-80-99'
        );
    }

    public function testSearchStationWithValidDateAndInvalidTimeThrowsEzception(): void
    {
        $this->expectException(InvalidTimeFormat::class);
        $this->expectExceptionMessage("'9999' is not a valid time - hhmm");

        $service = $this->makeLocationService();
        $service->search(
            station: 'KDY',
            date: '2023/08/19',
            time: '9999'
        );
    }

    public function testSearchStationWithValidDateAndNoTimeHitsCorrectEndpoint(): void
    {
        $this->client->get('json/search/KDY/2023/08/19')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));

        $service = $this->makeLocationService();
        $service->search(
            station: 'KDY',
            date: '2023/08/19',
        );
    }

    public function testSearchStationWithValidDateAndTimeHitsCorrectEndpoint(): void
    {
        $this->client->get('json/search/KDY/2023/08/19/2345')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));

        $service = $this->makeLocationService();
        $service->search(
            station: 'KDY',
            date: '2023/08/19',
            time: '2345'
        );
    }

    public function testSearchStationWithTimeAndNoDateIsIgnored(): void
    {
        $this->client->get('json/search/KDY')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));

        $service = $this->makeLocationService();
        $service->search(
            station: 'KDY',
            time: '9999'
        );
    }

    public function testSearchStationToStationWithValidDateAndNoTimeHitsCorrectEndpoint(): void
    {
        $this->client->get('json/search/KDY/to/DAM/2023/08/19')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));

        $service = $this->makeLocationService();
        $service->search(
            station: 'KDY',
            toStation: 'DAM',
            date: '2023/08/19',
        );
    }

    public function testSearchStationToStationWithValidDateAndTimeHitsCorrectEndpoint(): void
    {
        $this->client->get('json/search/KDY/to/DAM/2023/08/19/2345')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));

        $service = $this->makeLocationService();
        $service->search(
            station: 'KDY',
            toStation: 'DAM',
            date: '2023/08/19',
            time: '2345'
        );
    }

    public function testSearchStationToStationWithTimeAndNoDateIsIgnored(): void
    {
        $this->client->get('json/search/KDY/to/DAM')->shouldBeCalled()->willReturn(new Response(200, [], '{}'));

        $service = $this->makeLocationService();
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

        $this->client->get('json/search/XXXXXX')->shouldBeCalled()->willReturn(new Response(404, [], '{}'));

        $service = $this->makeLocationService();
        $service->search(
            station: 'XXXXXX',
        );
    }

    public function testSearchThirdPartyErrorThrowsException(): void
    {
        $this->expectException(ServiceNotFound::class);
        $this->expectExceptionMessage("Could not find service from 'json/search/XXXXXX'. Please check url.");

        $this->client->get('json/search/XXXXXX')->shouldBeCalled()->willReturn(
            new Response(200, [], '{"error":"Unknown error"}')
        );

        $service = $this->makeLocationService();
        $service->search(
            station: 'XXXXXX',
        );
    }

    public function testUnknownErrorThrowsException(): void
    {
        $this->expectException(Exception::class);

        $this->client->get('json/search/KDY')->shouldBeCalled()->willThrow(new Exception('error', 500));

        $service = $this->makeLocationService();
        $service->search(station: 'KDY');
    }

    private function makeLocationService(): LocationService
    {
        return new LocationService($this->client->reveal());
    }
}
